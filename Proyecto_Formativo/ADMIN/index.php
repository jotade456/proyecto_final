<?php
session_start();



// Redirección si no está logueado
if (!isset($_SESSION['administrador'])) {
    header("Location: login.php");
    exit;
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
            header("Location: index.php?success=1");
        } else {
            header("Location: index.php?error=1");
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
    $stmt = $conn->prepare("SELECT id, nombre, valor_producto, imagen_producto FROM productos WHERE TRIM(nombre) = ?");
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

    $id_usuario = 1; // Asumimos que aquí está el ID correcto

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

            // Obtener el nombre del producto
            $stmt_producto = $conn->prepare("SELECT nombre, imagen_producto FROM productos WHERE id = ?");
            $stmt_producto->bind_param("i", $id_producto);
            $stmt_producto->execute();
            $res_producto = $stmt_producto->get_result();
            $producto = $res_producto->fetch_assoc();
            $stmt_producto->close();

            $nombre_producto = $producto ? $producto['nombre'] : 'Producto desconocido';
            $imagen_producto = $producto ? $producto['imagen_producto'] : 'imagenes_productos/default.webp';

            echo json_encode([
                'success' => true,
                'message' => 'Cotización y detalle registrados',
                'total' => $total,
                'nombre_producto' => $nombre_producto,
                'imagen_producto' => $imagen_producto
            ]);
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
    src="https://kit.fontawesome.com/81581fb069.js"
    crossorigin="anonymous"
    </script>
</head>

<body>
    <header class="header__container">
        <nav class="navbar">
            <div class="logo">
                <img src="../imagenes/Logo_Roma.png" alt="Logo Roma">
            </div>
            <div class="action">
                <div class="profile" onclick="menuToggle()">
                    <img src="../imagenes/1.jpg" alt="Foto de perfil del administrador">
                </div>
                <div class="menu">
                    <h3>Keynner Jaimes<br> <span>Administrador</span></h3>
                    <ul>
                        <li><img src="../imagenes/user.png" alt=""><a href="#" onclick="abrirModal()">Agregar Producto</a></li>
                        <li><img src="../imagenes/edit.png" alt=""><a href="#" onclick="abrirModalModificar()">Modificar Producto</a></li>
                        <li><img src="../imagenes/settings.png" alt=""><a href="#" onclick="abrirModalEliminar()">Eliminar Producto</a></li>
                        <li><img src="../imagenes/envelope.png" alt=""><a href="#">Ver Historial</a></li>
                        <li><img src="../imagenes/question.png" alt=""><a href="#">Editar Perfil</a></li>
                        <li><img src="../imagenes/log-out.png" alt=""><a href="../inicio_sesion_multiservicios/logout.php">Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="welcome-message">
            <h2>Bienvenido, Keynner</h2>
        </div>
        <div class="search-input-box">
            <input type="text" id="buscadorCotizar" placeholder="Buscar producto...">
            <a href="#" onclick="event.preventDefault(); abrirModalP1();buscarProductoParaCotizar()">
                <i class="fa-solid fa-magnifying-glass icon"></i>
            </a>
            <ul class="container-suggestions">
            </ul>
        </div>
    </div>


    <!----- MODAL AGREGAR PRODUCTO ----->
    <div class="modal-producto" id="modalProducto">
        <div class="modal-content">
            <div class="modal-header">
                <span class="back-arrow" onclick="cerrarModal()">&larr;</span>
                <h2>Agregar Producto</h2>
            </div>
            <div class="modal-body">
                
                <form method="POST" action="index.php" class="formulario-producto" enctype="multipart/form-data">
                    <div method="POST" class="imagen-box" onclick="document.getElementById('inputImagen').click();">
                        <input name="imagen_producto" type="file" id="inputImagen" onchange="mostrarImagen(event)">
                        <img id="previewImagen" style="display: none;">
                        <p>Haz clic para subir imagen</p>
                    </div>

                    <input name="nombre_producto" type="text" id="nombreProductoAgregar" placeholder="Nombre del Producto" required>
                    <input name="valor_producto" type="number" id="valorProductoAgregar" placeholder="Valor del Producto" required>
                    <button type="submit">Agregar</button>
                </form>
                <div class="ventana-emergente" id="ventanaEmergente">
                    <div class="ventana-contenido">
                        <div class="ventana-encabezado">
                            <h2>Agregar producto</h2>
                        </div>
                        <div class="ventana-cuerpo">
                            <p>Producto agregado con éxito</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!---- MODAL MODIFICAR PRODUCTO ---->
    <div class="modal-modificar" id="modalmodificar">
        <div class="modal-content-modificar">
            <div class="modal-header-modificar">
                <span class="back-arrow" onclick="cerrarModalModificar()">&larr;</span>
                <h2>Modificar Producto</h2>
            </div>
            <div class="modal-body-modificar">
                <div class="buscador-modificar">
                    <form method="POST" action="index.php">
                        <input type="text" name="buscar_nombre" id="inputBuscarModificar" placeholder="Buscar Producto" required>
                        <button type="submit"><i class="fa-solid fa-magnifying-glass icon"></i></button>
                    </form>
                    <ul class="contenedor-sugerencias" id="contenedorSugerenciasModificar">
                    </ul>
                </div>
            </div>    
        </div>
    </div>


    <div class="modal-producto-mod" id="modalProductoModificar">
        <div class="modal-content-mod">
            <div class="modal-header-mod">
                <span class="back-arrow" onclick="cerrarModalMod()">&larr;</span>
                <h2>Modificar Producto</h2>
            </div>
            <div class="modal-body-mod">
                <div class="imagen-box-mod" onclick="document.getElementById('inputImagen-mod').click();">
                    <input type="file" id="inputImagen-mod" onchange="mostrarImagenMod(event)">
                    <img id="previewImagen-mod" style="display: none;">                
                    <p>Modificar Imagen</p>
                </div>
                <form class="formulario-producto-mod" method="POST" action="index.php">
                    <input type="hidden" name="id_producto" id="idProducto-mod">
                    <input name="nuevo_nombre" type="text" id="nombreProducto-mod" placeholder="Modificar Nombre" required>
                    <input name="nuevo_valor" type="number" id="valorProducto-mod" placeholder="Modificar Valor" required>
                    <button type="submit">Modificar</button>
                </form>
                <div class="ventana-emergente-mod" id="ventanaEmergenteModificar">
                    <div class="ventana-contenido-mod">
                        <div class="ventana-encabezado-mod">
                            <h2>Modificar producto</h2>
                        </div>
                        <div class="ventana-cuerpo-mod">
                            <p>Producto Modificado con éxito</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!---- MODAL ELIMINAR PRODUCTO ----->
    <div class="modal-eliminar" id="modaleliminar">
        <div class="modal-content-eliminar">
            <div class="modal-header-eliminar">
                <span class="back-arrow" onclick="cerrarModalEliminar()">&larr;</span>
                <h2>Eliminar Producto</h2>
            </div>
            <div class="modal-body-eliminar">
                <div class="buscador-eliminar">
                    <form method="POST" action="index.php">
                        <input type="text" name="buscar_nombre" id="inputBuscarEliminar" placeholder="Buscar Producto" required>
                        <button type="submit" onclick="abrirModalEli()"><i class="fa-solid fa-magnifying-glass icon"></i></button>
                    </form>
                    <ul class="contenedor-sugerencias-eliminar" id="contenedorSugerenciasEliminar">
                    </ul>
                </div>
            </div>    
        </div>
    </div>


    <div class="modal-producto-eli" id="modalProductoEliminar">
        <div class="modal-content-eli">
            <div class="modal-header-eli">
                <span class="back-arrow" onclick="cerrarModalEli()">&larr;</span>
                <h2>Eliminar Producto</h2>
            </div>
            <div class="modal-body-eli">
                <div class="imagen-box-eli" onclick="document.getElementById('inputImagen-eli').click();">
                    <input type="file" id="inputImagen-eli" onchange="mostrarImagenEli(event)">
                    <img id="previewImagen-eli" style="display: none;">                
                    <p>Eliminar Imagen</p>
                </div>
                <form class="formulario-producto-eli" method="POST" action="index.php">
                    <input type="hidden" name="id_producto" id="idProducto-eli">
                    <input name="eliminar_nombre" type="text" id="nombreProducto-eli" placeholder="Eliminar Nombre" required>
                    <input name="eliminar_valor" type="number" id="valorProducto-eli" placeholder="Eliminar Valor" required>
                    <button type="submit">Eliminar</button>
                </form>
                <div class="ventana-emergente-eli" id="ventanaEmergenteEliminar">
                    <div class="ventana-contenido-eli">
                        <div class="ventana-encabezado-eli">
                            <h2>Eliminar producto</h2>
                        </div>
                        <div class="ventana-cuerpo-eli">
                            <p>Producto Eliminado con éxito</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



        <!-- MODAL PÁGINA PRINCIPAL 1 -->
    <div class="modal-principal1" id="modalPrincipal1">
        <div class="modal-contentP1">
            <div class="modal-headerP1">
                <span class="back-arrow" onclick="cerrarModalP1()">&larr;</span>
            </div>
            <div class="modal-bodyP1">
                <div class="imagen-boxP1">
                    <input type="file" id="inputImagenP1" onchange="mostrarImagenP1(event)">
                    <img id="previewImagenP1" style="display: none;">                
                </div>
                <form class="formulario-P1" id="formularioCotizarP1">
                    <div class="grupo-nombrep1">
                        <label for="nombreProductoP1">Nombre del Producto</label>
                        <input type="text" id="nombreProductoP1" placeholder="" readonly>
                    </div>     
                    <div class="grupo-valorp1">
                        <label for="valorProductoP1">Valor del Producto</label>
                        <input type="number" id="valorProductoP1" placeholder="" readonly>
                    </div> 

                    <!-- Campo oculto para guardar id del producto -->
                    <input type="hidden" id="idProductoP1" value="">

                    <button type="button" onclick="buscarProductoParaCotizar()">Cotizar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL PÁGINA PRINCIPAL 2 -->
    <div class="modal-principal2" id="modalPrincipal2">
        <div class="modal-contentP2">
            <div class="modal-headerP2">
                <span class="back-arrow" onclick="cerrarModalP2()">&larr;</span>
            </div>
            <div class="modal-bodyP2">
                <form method="POST" class="formulario-P2" id="formularioCotizarP2" enctype="multipart/form-data" onsubmit="return false;">
                    <div class="imagen-boxP2">
                        <input name="imagen_producto_cotizada" type="file" id="inputImagenP2" onchange="mostrarImagenP2(event)">
                        <img id="previewImagenP2" style="display: none;">                
                    </div>
                    <div class="grupo-cantidadP2">
                        <label for="cantidadProductoP2">Cantidad del Producto</label>
                        <input name="cantidad_producto_cotizar" type="number" id="cantidadProductoP2" placeholder="Cantidad" required>
                    </div>     
                    <div class="grupo-valorImpresionP2">
                        <label for="valorImpresionP2">Valor de impresión</label>
                        <input name="valor_impresion_cotizar" type="number" id="valorImpresionP2" placeholder="Si se requiere">
                    </div>
                    <div class="grupo-valorDiseñoP2">
                        <label for="valorDiseñoP2">Valor del Diseño</label>
                        <input name="valor_diseño_cotizar" type="number" id="valorDiseñoP2" placeholder="$$$" required>
                    </div>

                    <!-- Campo oculto para enviar el valor del producto -->
                    <input type="hidden" name="valor_producto_cotizar" id="valorProductoOcultoP2" >

                    <!-- Campo oculto NUEVO para enviar el id del producto -->
                    <input type="hidden" name="id_producto" id="idProductoOcultoP2" >

                    <button type="submit">Cotizar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL PÁGINA PRINCIPAL 3 -->
    <div class="modal-principal3" id="modalPrincipal3">
        <div class="modal-contentP3">
            <div class="modal-headerP3">
                <span class="back-arrow" onclick="cerrarModalP3()">&larr;</span>
            </div>
            <div class="modal-bodyP3">
                <div class="imagen-boxP3">
                    <input type="file" id="inputImagenP3" onchange="mostrarImagenP3(event)">
                    <img id="previewImagenP3" style="display: none;">                
                </div>
                <form class="formulario-P3" id="formularioCotizarP3">
                    <div class="grupo-nombreP3">
                        <label for="nombreProductoP3">Nombre del Producto</label>
                        <input type="text" id="nombreProductoP3" placeholder="" readonly>
                    </div>     
                    <div class="grupo-totalP3">
                        <label for="precioTotalP3">Precio Total</label>
                        <input type="number" id="precioTotalP3" placeholder="" readonly>
                    </div> 
                    <button type="button"  onclick="cerrarModalP3()">Cotizar</button>
                </form>
                <div class="ventana-emergenteP3" id="ventanaEmergenteP3" style="display:none;">
                    <div class="ventana-contenidoP3">
                        <div class="ventana-encabezadoP3">
                            <h2>Cotizar</h2>
                        </div>
                        <div class="ventana-cuerpoP3">
                            <p>Cotización guardada con éxito</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-------  MODAL HISTORIAL DE COTIZACIONES ------>    
    <div class="modal-historial" id="modalHistorial">
        <div class="modal-content-historial">
            <div class="modal-header-historial">
                <span class="back-arrow-historial" onclick="cerrarModalHistorial()">&larr;</span>
                <h2>Historial de Cotizaciones</h2>
            </div>
            <div class="modal-body-historial">
                <div class="historial-cotizaciones">
                    <div class="historial-contenedor">

                        <div class="tarjeta-cotizacion">
                            <div>
                                <p class="titulo-cotizacion">Cotización 7</p>
                                <p class="monto-cotizacion">$5000</p>
                            </div>
                            <div class="info-cotizacion">
                                <span class="fecha-cotizacion">15/03/2025</span>
                                <button class="btn-pdf" onclick="descargarPDF(7)">PDF</button>
                            </div>
                        </div>

                        <div class="tarjeta-cotizacion">
                            <div>
                                <p class="titulo-cotizacion">Cotización 6</p>
                                <p class="monto-cotizacion">$7300</p>
                            </div>
                            <div class="info-cotizacion">
                                <span class="fecha-cotizacion">13/03/2025</span>
                                <button class="btn-pdf" onclick="descargarPDF(6)">PDF</button>
                            </div>
                        </div>

                        <div class="tarjeta-cotizacion">
                            <div>
                                <p class="titulo-cotizacion">Cotización 5</p>
                                <p class="monto-cotizacion">$9100</p>
                            </div>
                            <div class="info-cotizacion">
                                <span class="fecha-cotizacion">12/03/2025</span>
                                <button class="btn-pdf" onclick="descargarPDF(5)">PDF</button>
                            </div>
                        </div>

                        <div class="tarjeta-cotizacion">
                            <div>
                                <p class="titulo-cotizacion">Cotización 4</p>
                                <p class="monto-cotizacion">$9100</p>
                            </div>
                            <div class="info-cotizacion">
                                <span class="fecha-cotizacion">10/03/2025</span>
                                <button class="btn-pdf" onclick="descargarPDF(4)">PDF</button>
                            </div>
                        </div>

                        <div class="tarjeta-cotizacion">
                            <div>
                                <p class="titulo-cotizacion">Cotización 3</p>
                                <p class="monto-cotizacion">$9100</p>
                            </div>
                            <div class="info-cotizacion">
                                <span class="fecha-cotizacion">09/03/2025</span>
                                <button class="btn-pdf" onclick="descargarPDF(3)">PDF</button>
                            </div>
                        </div>

                        <div class="tarjeta-cotizacion">
                            <div>
                                <p class="titulo-cotizacion">Cotización 2</p>
                                <p class="monto-cotizacion">$9100</p>
                            </div>
                            <div class="info-cotizacion">
                                <span class="fecha-cotizacion">09/03/2025</span>
                                <button class="btn-pdf" onclick="descargarPDF(2)">PDF</button>
                            </div>
                        </div>

                        <div class="tarjeta-cotizacion">
                            <div>
                                <p class="titulo-cotizacion">Cotización 1</p>
                                <p class="monto-cotizacion">$9100</p>
                            </div>
                            <div class="info-cotizacion">
                                <span class="fecha-cotizacion">02/03/2025</span>
                                <button class="btn-pdf" onclick="descargarPDF(1)">PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        function menuToggle() {
            const toggleMenu = document.querySelector(".menu");
            toggleMenu.classList.toggle('active');
        }
    
        document.addEventListener("click", function(e) {
            const menu = document.querySelector(".menu");
            const profile = document.querySelector(".profile");
    
            if (!menu.contains(e.target) && !profile.contains(e.target)) {
                menu.classList.remove("active");
            }
        });

        
    </script>
    <script src="main.js"></script>
    <script src="modal.js"></script>
</body>
</html>