<?php
session_start();
error_log('ID usuario en sesión: ' . (isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 'NO DEFINIDO'));


if (!isset($_SESSION['administrador']) && !isset($_SESSION['empleado'])) {
    
    if (
        isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'
    ) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No autorizado']);
        exit;
    } else {
        header("Location: ../inicio_sesion_multiservicios/login.php");
        exit;
    }
}

// Manejo de peticiones POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Buscar producto por nombre
    if (isset($_POST['ajax_buscar_nombre'])) {
        header('Content-Type: application/json');
        $nombre = trim($_POST['ajax_buscar_nombre']);
        $producto = obtener_producto_por_nombre($nombre);
        echo json_encode($producto ?: ['error' => 'Producto no encontrado']);
        exit;
    }

    if (isset($_POST['ajax_sugerencias'])) {
        header('Content-Type: application/json');
        $keyword = trim($_POST['ajax_sugerencias']);
        $sugerencias = obtener_sugerencias_por_nombre($keyword);
        echo json_encode($sugerencias);
        exit;
    }

    
    // Cotizar producto 
    if (
        isset($_POST['cantidad_producto_cotizar']) &&
        isset($_POST['valor_producto_cotizar']) &&
        isset($_POST['valor_diseño_cotizar']) &&
        isset($_POST['id_producto'])
    ) {
        $cantidad = $_POST['cantidad_producto_cotizar'];
        $valor_producto = $_POST['valor_producto_cotizar'];
        $valor_diseño = $_POST['valor_diseño_cotizar'];
        $valor_impresion = isset($_POST['valor_impresion_cotizar']) ? $_POST['valor_impresion_cotizar'] : 0;
        $id_producto = $_POST['id_producto'];
        cotizar_productos($cantidad, $valor_producto, $valor_diseño, $valor_impresion, $id_producto);
        exit;
    }
    //Agregar producto

    if (
        isset($_POST['nombre_producto']) &&
        isset($_POST['valor_producto']) &&
        isset($_FILES['imagen_producto'])
    ) {
        $nombre = $_POST['nombre_producto'];
        $valor = $_POST['valor_producto'];

        if (!is_numeric($valor) || floatval($valor) < 0) {
            echo "ERROR_VALOR_NEGATIVO";
            exit;
        }

        if ($_FILES['imagen_producto']['error'] !== 0 || $_FILES['imagen_producto']['size'] == 0) {
            echo "ERROR_IMAGEN_VACIA";
            exit;
        }

        $destino = 'imagenes_productos/';
        if (!file_exists($destino)) {
            mkdir($destino, 0777, true);
        }

        $nombreImagen = time() . "_" . basename($_FILES['imagen_producto']['name']);
        $rutaImagen = $destino . $nombreImagen;

        if (!move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $rutaImagen)) {
            echo "ERROR_COPIAR_IMAGEN";
            exit;
        }

        $resultado = agregar_producto($nombre, $valor, $rutaImagen);

        if ($resultado === 'EXISTE') {
            echo "ERROR_PRODUCTO_EXISTE";
        } elseif ($resultado === true) {
            echo "AGREGADO_OK";
        } else {
            echo "ERROR_INSERTAR";
        }

        exit;
    }

    // modificar producto
    if (
        isset($_POST['id_producto']) &&
        isset($_POST['nuevo_nombre']) &&
        isset($_POST['nuevo_valor'])
    ) {
        $id = $_POST['id_producto'];
        $nombre = $_POST['nuevo_nombre'];
        $valor = $_POST['nuevo_valor'];

        if (!is_numeric($valor) || $valor < 0) {
            echo json_encode([
                'success' => false,
                'message' => '❌ El valor no puede ser negativo.'
            ]);
            exit;
        }

        $resultado = modificar_producto($id, $nombre, $valor);

        echo json_encode([
            'success' => $resultado,
            'message' => $resultado ? '✅ Producto modificado correctamente' : '❌ Error al modificar'
        ]);
        exit;
    }



    

    // Eliminar (inhabilitar) producto
    if (isset($_POST['eliminar_producto'])) {
        $id_producto = $_POST['eliminar_producto'];
        if (eliminar_producto($id_producto)) {
            echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al eliminar el producto']);
        }
        exit;
    }

    if (isset($_POST['obtener_datos_usuario'])) {
        header('Content-Type: application/json');

        $id_usuario = $_SESSION['id_usuario'];
        $datos = obtener_datos_usuario($id_usuario);

        echo json_encode($datos ?: ['error' => 'Usuario no encontrado']);
        exit;
    }

    // Actualizar datos de usuario logueado
    if (isset($_POST['actualizar_datos_usuario'])) {
        header('Content-Type: application/json');

        $id_usuario = $_SESSION['id_usuario'];
        $nombre = trim($_POST['nombre']);
        $correo = trim($_POST['correo']);
        $nueva_contrasena = trim($_POST['contrasena']);
        $nueva_contrasena = $nueva_contrasena !== '' ? $nueva_contrasena : null;

        $resultado = actualizar_datos_usuario($id_usuario, $nombre, $correo, $nueva_contrasena);

        if ($resultado['success']) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $resultado['error'] ?? 'No se pudo actualizar']);
        }
        exit;
    }
}


// ================= FUNCIONES =================

function conectarBD() {
    $conn = new mysqli("localhost", "root", "", "cotizacionesmagicas");
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    return $conn;
}

function agregar_producto($nombre, $valor, $imagen) {
    $conn = conectarBD();

    $stmt_check = $conn->prepare("SELECT id FROM productos WHERE nombre = ?");
    $stmt_check->bind_param("s", $nombre);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        $conn->close();
        return "EXISTE";
    }

    $stmt_check->close();

    $stmt = $conn->prepare("INSERT INTO productos (nombre, valor_producto, imagen_producto) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $nombre, $valor, $imagen);
    $resultado = $stmt->execute();
    $stmt->close();
    $conn->close();

    return $resultado;
}



function modificar_producto($id, $nombre, $valor) {
    $conn = conectarBD();

    if (isset($_FILES['nueva_imagen']) && $_FILES['nueva_imagen']['error'] === UPLOAD_ERR_OK) {
        $destino = 'imagenes_productos/';
        if (!file_exists($destino)) mkdir($destino, 0777, true);

        $nombreImagen = time() . "_" . basename($_FILES['nueva_imagen']['name']);
        $rutaImagen = $destino . $nombreImagen;

        if (move_uploaded_file($_FILES['nueva_imagen']['tmp_name'], $rutaImagen)) {
            $stmt = $conn->prepare("UPDATE productos SET nombre = ?, valor_producto = ?, imagen_producto = ? WHERE id = ?");
            $stmt->bind_param("sdsi", $nombre, $valor, $rutaImagen, $id);
        } else {
            echo json_encode(['success' => false, 'error' => 'Error al subir imagen']);
            $conn->close();
            exit;
        }
    } else {
        $stmt = $conn->prepare("UPDATE productos SET nombre = ?, valor_producto = ? WHERE id = ?");
        $stmt->bind_param("sdi", $nombre, $valor, $id);
    }

    $resultado = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $resultado;
}

function obtener_producto_por_nombre($nombre) {
    $conn = conectarBD();
    $stmt = $conn->prepare("SELECT id, nombre, valor_producto, imagen_producto FROM productos WHERE TRIM(nombre) = ? AND estado = 'activo'");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $res = $stmt->get_result();
    $producto = $res->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $producto;
}

function obtener_sugerencias_por_nombre($keyword) {
    $conn = conectarBD();
    $stmt = $conn->prepare("SELECT nombre FROM productos WHERE nombre LIKE CONCAT('%', ?, '%') AND estado = 'activo' LIMIT 5");
    $stmt->bind_param("s", $keyword);
    $stmt->execute();
    $res = $stmt->get_result();

    $sugerencias = [];
    while ($row = $res->fetch_assoc()) {
        $sugerencias[] = $row['nombre'];
    }

    $stmt->close();
    $conn->close();

    return $sugerencias;
}
// Buscar sugerencias por nombre
if (isset($_GET['accion']) && $_GET['accion'] === 'sugerencias') {
    $keyword = $_GET['buscar'] ?? '';
    $sugerencias = obtener_sugerencias_por_nombre($keyword);
    echo json_encode($sugerencias);
    exit;
}

function cotizar_productos($cantidad, $valor_producto, $valor_diseño, $valor_impresion, $id_producto) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    header('Content-Type: application/json');

    try {
        $conn = conectarBD();
        $conn->begin_transaction();

        // Verificar sesión
        if (!isset($_SESSION['administrador']) && !isset($_SESSION['empleado'])) {
            echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
            return;
        }

        if (isset($_SESSION['id_usuario'])) {
            $id_usuario = $_SESSION['id_usuario'];
        } else {
            throw new Exception('Usuario no autenticado (sin ID)');
        }

        // Verificar que el usuario existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 0) {
            throw new Exception('ID de usuario inválido');
        }
        $stmt->close();

        // Validaciones
        if (!is_numeric($cantidad) || $cantidad <= 0 ||
            !is_numeric($valor_producto) || $valor_producto <= 0 ||
            !is_numeric($valor_diseño) || $valor_diseño < 0) {
            throw new Exception('Datos inválidos');
        }

        $valor_impresion = is_numeric($valor_impresion) ? max(0, $valor_impresion) : 0;
        $subtotal = $cantidad * $valor_producto;
        $total = $subtotal + $valor_diseño + $valor_impresion;
        $fecha = date("Y-m-d");

        // Insertar cotización
        $stmt = $conn->prepare("INSERT INTO cotizaciones (fecha, id_usuario, total) VALUES (?, ?, ?)");
        $stmt->bind_param("sid", $fecha, $id_usuario, $total);
        $stmt->execute();

        $id_cotizacion = $conn->insert_id;
        $stmt->close();

        // Insertar detalle de cotización
        $stmt_detalle = $conn->prepare("
            INSERT INTO detalle_cotizacion (id_cotizacion, id_producto, cantidad, precio_unitario, subtotal) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt_detalle->bind_param("iiidd", $id_cotizacion, $id_producto, $cantidad, $valor_producto, $subtotal);
        $stmt_detalle->execute();
        $stmt_detalle->close();

        // Obtener datos del producto activo
        $stmt_producto = $conn->prepare("SELECT nombre, imagen_producto FROM productos WHERE id = ? AND estado = 'activo'");
        $stmt_producto->bind_param("i", $id_producto);
        $stmt_producto->execute();
        $res_producto = $stmt_producto->get_result();
        $producto = $res_producto->fetch_assoc();
        $stmt_producto->close();

        if (!$producto) {
            throw new Exception('El producto no existe o está inactivo');
        }

        $nombre_producto = $producto['nombre'];
        $imagen_producto = $producto['imagen_producto'];

        //  Insertar en tabla HISTORIAL
        $stmt_historial = $conn->prepare("
            INSERT INTO historial 
            (id_usuario, fecha, nombre_producto, cantidad, precio_unitario, subtotal, total_cotizacion) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt_historial->bind_param(
            "issiddd",
            $id_usuario,
            $fecha,
            $nombre_producto,
            $cantidad,
            $valor_producto,
            $subtotal,
            $total
        );
        $stmt_historial->execute();
        $stmt_historial->close();

        // Confirmar transacción
        $conn->commit();

        // Respuesta exitosa al frontend
        echo json_encode([
            'success' => true,
            'message' => 'Cotización realizada con éxito',
            'total' => $total,
            'nombre_producto' => $nombre_producto,
            'imagen_producto' => $imagen_producto
        ]);

    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollback();
        }

        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    } finally {
        if (isset($conn)) {
            $conn->close();
        }
    }
}

function eliminar_producto($id_producto) {
    $conn = conectarBD();
    $stmt = $conn->prepare("UPDATE productos SET estado = 'inactivo' WHERE id = ?");
    $stmt->bind_param("i", $id_producto);
    $resultado = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $resultado;
}

function obtener_datos_usuario($id_usuario) {
    $conn = conectarBD();
    $stmt = $conn->prepare("SELECT nombre_usuario, correo, rol, foto_perfil FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $res = $stmt->get_result();
    $usuario = $res->fetch_assoc();
    $stmt->close();
    $conn->close();

    // Si tiene imagen
    if ($usuario && !empty($usuario['foto_perfil'])) {
        // Construir ruta física
        $ruta_fisica = __DIR__ . '/imagenes_perfil/' . $usuario['foto_perfil'];

        // Si la imagen existe
        if (file_exists($ruta_fisica)) {
            // Devolver la ruta que el navegador puede usar
            $usuario['foto_perfil'] = '../controlador/imagenes_perfil/' . $usuario['foto_perfil'];
        } else {
            // Imagen no existe → null
            $usuario['foto_perfil'] = null;
        }
    } else {
        // No hay imagen en BD → null
        $usuario['foto_perfil'] = null;
    }

    return $usuario;
}


function actualizar_datos_usuario($id_usuario, $nombre, $correo, $nueva_contrasena = null) {
    $conn = conectarBD();

    // ✅ Verificar que el correo no esté en uso por otro usuario
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = ? AND id != ?");
    $stmt->bind_param("si", $correo, $id_usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Ya existe otro usuario con ese correo
        $stmt->close();
        $conn->close();
        return ['success' => false, 'error' => 'Este correo ya está registrado por otro usuario'];
    }
    $stmt->close();

    $foto_perfil = null;

    // ✅ Subir nueva imagen si fue enviada
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $destino = 'imagenes_perfil/';
        if (!file_exists($destino)) mkdir($destino, 0777, true);

        $nombreImagen = time() . "_" . basename($_FILES['foto_perfil']['name']);
        $rutaImagen = $destino . $nombreImagen;

        if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $rutaImagen)) {
            $foto_perfil = $nombreImagen;
        }
    }

    // ✅ Preparar sentencia SQL
    if ($nueva_contrasena) {
        $hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        if ($foto_perfil) {
            $stmt = $conn->prepare("UPDATE usuarios SET nombre_usuario = ?, correo = ?, contraseña = ?, foto_perfil = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $nombre, $correo, $hash, $foto_perfil, $id_usuario);
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET nombre_usuario = ?, correo = ?, contraseña = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nombre, $correo, $hash, $id_usuario);
        }
    } else {
        if ($foto_perfil) {
            $stmt = $conn->prepare("UPDATE usuarios SET nombre_usuario = ?, correo = ?, foto_perfil = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nombre, $correo, $foto_perfil, $id_usuario);
        } else {
            $stmt = $conn->prepare("UPDATE usuarios SET nombre_usuario = ?, correo = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nombre, $correo, $id_usuario);
        }
    }

    $resultado = $stmt->execute();
    $stmt->close();
    $conn->close();

    return ['success' => $resultado];
}



?>