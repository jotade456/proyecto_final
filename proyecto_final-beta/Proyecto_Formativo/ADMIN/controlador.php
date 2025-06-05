<?php
session_start();
error_log('ID usuario en sesión: ' . (isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 'NO DEFINIDO'));


// Si no está logueado como administrador...
if (!isset($_SESSION['administrador'])) {
    // Si es una petición AJAX (fetch)
    if ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No autorizado']);
        exit;
    } else {
        // Redirigir si es navegación normal (no AJAX)
        header("Location: login.php");
        exit;
    }
}

// Manejo de peticiones POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Buscar producto por nombre (AJAX)
    if (isset($_POST['ajax_buscar_nombre'])) {
        header('Content-Type: application/json');
        $nombre = trim($_POST['ajax_buscar_nombre']);
        $producto = obtener_producto_por_nombre($nombre);
        echo json_encode($producto ?: ['error' => 'Producto no encontrado']);
        exit;
    }

    // Cotizar producto (AJAX)
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

    // Agregar producto
    if (
        isset($_POST['nombre_producto']) &&
        isset($_POST['valor_producto']) &&
        isset($_FILES['imagen_producto'])
    ) {
        $nombre = $_POST['nombre_producto'];
        $valor = $_POST['valor_producto'];

        $imagen = '';
        $destino = 'imagenes_productos/';
        if (!file_exists($destino)) {
            mkdir($destino, 0777, true);
        }

        $nombreImagen = time() . "_" . basename($_FILES['imagen_producto']['name']);
        $rutaImagen = $destino . $nombreImagen;

        if (move_uploaded_file($_FILES['imagen_producto']['tmp_name'], $rutaImagen)) {
            $imagen = $rutaImagen;
        } else {
            $imagen = 'imagenes_productos/default.webp';
        }

        if (agregar_producto($nombre, $valor, $imagen)) {
            header("Location: index.html?success=1");
        } else {
            header("Location: index.html?error=1");
        }
        exit;
    }

    // Modificar producto
    if (
        isset($_POST['id_producto']) &&
        isset($_POST['nuevo_nombre']) &&
        isset($_POST['nuevo_valor'])
    ) {
        modificar_producto($_POST['id_producto'], $_POST['nuevo_nombre'], $_POST['nuevo_valor']);
        header("Location: index.html?success=1");
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
        header("Location: index.html?success=1");
        exit;
    }
    // Obtener datos de usuario logueado (para llenar el formulario de perfil)
    if (isset($_POST['obtener_datos_usuario'])) {
        header('Content-Type: application/json');

        $id_usuario = $_SESSION['id_usuario']; // 🚀 id de la sesión
        $datos = obtener_datos_usuario($id_usuario);

        echo json_encode($datos ?: ['error' => 'Usuario no encontrado']);
        exit;
    }

    // Actualizar datos de usuario logueado (al hacer clic en "Aplicar cambios")
    if (isset($_POST['actualizar_datos_usuario'])) {
        header('Content-Type: application/json');

        $id_usuario = $_SESSION['id_usuario']; // 🚀 id de la sesión
        $nombre = trim($_POST['nombre']);
        $correo = trim($_POST['correo']);
        $nueva_contrasena = trim($_POST['contrasena']);
        $nueva_contrasena = $nueva_contrasena !== '' ? $nueva_contrasena : null;

        $resultado = actualizar_datos_usuario($id_usuario, $nombre, $correo, $nueva_contrasena);

        if ($resultado) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se pudo actualizar']);
        }
        exit;
    }

    
}




// Mostrar alertas al navegar (GET)
if (isset($_GET['success'])) {
    echo "<script>alert('✅ Producto registrado exitosamente');</script>";
}
if (isset($_GET['error'])) {
    echo "<script>alert('❌ Error al registrar el producto');</script>";
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
    $stmt = $conn->prepare("INSERT INTO productos (nombre, valor_producto, imagen_producto) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $nombre, $valor, $imagen);
    $resultado = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $resultado;
}

function modificar_producto($id, $nombre, $valor) {
    $conn = conectarBD();
    $stmt = $conn->prepare("UPDATE productos SET nombre = ?, valor_producto = ? WHERE id = ?");
    $stmt->bind_param("sdi", $nombre, $valor, $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
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
function cotizar_productos($cantidad, $valor_producto, $valor_diseño, $valor_impresion, $id_producto) {
    header('Content-Type: application/json');
    $conn = conectarBD();

    if (!isset($_SESSION['administrador'])) {
        echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
        return;
    }

    if (isset($_SESSION['id_usuario'])) {
        $id_usuario = $_SESSION['id_usuario'];
    } else {
        echo json_encode(['success' => false, 'error' => 'Usuario no autenticado (sin ID)']);
        return;
    }

    // Verificar que el usuario existe en la base de datos
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'ID de usuario inválido']);
        $stmt->close();
        $conn->close();
        return;
    }
    $stmt->close();

    // Validaciones numéricas
    if (!is_numeric($cantidad) || $cantidad <= 0 ||
        !is_numeric($valor_producto) || $valor_producto <= 0 ||
        !is_numeric($valor_diseño) || $valor_diseño < 0) {
        echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
        $conn->close();
        return;
    }

    $valor_impresion = is_numeric($valor_impresion) ? max(0, $valor_impresion) : 0;
    $subtotal = $cantidad * $valor_producto;
    $total = $subtotal + $valor_diseño + $valor_impresion;
    $fecha = date("Y-m-d");

    // Insertar cotización
    $stmt = $conn->prepare("INSERT INTO cotizaciones (fecha, id_usuario, total) VALUES (?, ?, ?)");
    $stmt->bind_param("sid", $fecha, $id_usuario, $total);

    if ($stmt->execute()) {
        $id_cotizacion = $conn->insert_id;
        $stmt->close();

        // Insertar detalle de cotización
        $stmt_detalle = $conn->prepare("INSERT INTO detalle_cotizacion (id_cotizacion, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmt_detalle->bind_param("iiidd", $id_cotizacion, $id_producto, $cantidad, $valor_producto, $subtotal);

        if ($stmt_detalle->execute()) {
            $stmt_detalle->close();

            // Obtener el nombre del producto solo si está ACTIVO
            $stmt_producto = $conn->prepare("SELECT nombre, imagen_producto FROM productos WHERE id = ? AND estado = 'activo'");
            $stmt_producto->bind_param("i", $id_producto);
            $stmt_producto->execute();
            $res_producto = $stmt_producto->get_result();
            $producto = $res_producto->fetch_assoc();
            $stmt_producto->close();

            // Si no existe o está inactivo → error y no deja cotizar
            if (!$producto) {
                echo json_encode(['success' => false, 'error' => 'El producto no existe o está inactivo']);
                $conn->close();
                return;
            }

            $nombre_producto = $producto['nombre'];
            $imagen_producto = $producto['imagen_producto'];

            // 🚀 Insertar en el historial
            $stmt_historial = $conn->prepare("INSERT INTO historial (id_usuario, fecha, nombre_producto, cantidad, precio_unitario, subtotal, total_cotizacion) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_historial->bind_param("issiddi", $id_usuario, $fecha, $nombre_producto, $cantidad, $valor_producto, $subtotal, $total);

            if ($stmt_historial->execute()) {
                // ✅ Todo exitoso: cotización, detalle e historial registrados
                echo json_encode([
                    'success' => true,
                    'message' => 'Cotización, detalle e historial registrados',
                    'total' => $total,
                    'nombre_producto' => $nombre_producto,
                    'imagen_producto' => $imagen_producto
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al registrar en el historial']);
            }

            $stmt_historial->close();

        } else {
            echo json_encode(['success' => false, 'error' => 'Error al registrar detalle de cotización']);
            $stmt_detalle->close();
        }

    } else {
        echo json_encode(['success' => false, 'error' => 'Error al registrar la cotización']);
        $stmt->close();
    }

    $conn->close();
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
    $stmt = $conn->prepare("SELECT nombre_usuario, correo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $res = $stmt->get_result();
    $usuario = $res->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $usuario;
}

function actualizar_datos_usuario($id_usuario, $nombre, $correo, $nueva_contrasena = null) {
    $conn = conectarBD();

    if ($nueva_contrasena) {
        // Si el usuario puso nueva contraseña
        $hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuarios SET nombre_usuario = ?, correo = ?, contraseña = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nombre, $correo, $hash, $id_usuario);
    } else {
        // Si no, solo nombre y correo
        $stmt = $conn->prepare("UPDATE usuarios SET nombre_usuario = ?, correo = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nombre, $correo, $id_usuario);
    }

    $resultado = $stmt->execute();
    $stmt->close();
    $conn->close();

    return $resultado;
}

?>