<?php

session_start();

if (!isset($_SESSION['administrador'])) {
    // Evita cacheo de esta página
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Location: login.php");
    exit();
}

if (isset($_GET['success'])) {
    echo "<script>alert('✅ Producto registrado exitosamente');</script>";
}
if (isset($_GET['error'])) {
    echo "<script>alert('❌ Error al registrar el producto');</script>";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 🔁 NUEVO: para peticiones AJAX desde JavaScript (buscador con fetch)
    if (isset($_POST['ajax_buscar_nombre'])) {
        header('Content-Type: application/json');
        $nombre = trim($_POST['ajax_buscar_nombre']);
        $producto = obtener_producto_por_nombre($nombre);
        echo json_encode($producto ?: ['error' => 'Producto no encontrado']);
        exit;
    }

    // ✅ AGREGAR PRODUCTO 
    if (isset($_POST['nombre_producto']) && isset($_POST['valor_producto']) && isset($_FILES['imagen_producto'])) {
        $nombre = $_POST['nombre_producto'];
        $valor = $_POST['valor_producto'];
        
        // Guardar imagen
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
            $imagen = 'imagenes_productos/default.webp'; // ruta por defecto
        }

        // Guardar producto
        if (agregar_producto($nombre, $valor, $imagen)) {
            header("Location: index.php?success=1");
        } else {
            header("Location: index.php?error=1");
        }
        exit; // ¡Muy importante!
    }

    // ✅ Modificación desde formulario tradicional
    if (isset($_POST['id_producto']) && isset($_POST['nuevo_nombre']) && isset($_POST['nuevo_valor'])) {
        modificar_producto($_POST['id_producto'], $_POST['nuevo_nombre'], $_POST['nuevo_valor']);
    }


    // if(isset($_POST['id_producto']) && isset($_POST['eliminar_nombre']) && isset($_POST['eliminar_valor'])){
    //     eliminar_producto($_POST['id_producto'], $_POST['eliminado_nombre'], $_POST['eliminado_valor']);
    // }

    if(isset($_POST['cantidad_producto_cotizar']) && isset($_POST['valor_producto_cotizar']) && isset($_POST['valor_diseño_cotizar'])){
        $cant_prod_cotizar = $_POST['cantidad_producto_cotizar'];
        $val_prod_cotizar = $_POST['valor_producto_cotizar'];
        $val_diseño_cotizar = $_POST['valor_diseño_cotizar'];

        cotizar_productos($cant_prod_cotizar, $val_prod_cotizar, $val_diseño_cotizar);
        
    }
}

function conectarBD()
{
    $conn = new mysqli("localhost", "root", "", "multiservicios_roma");

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    return $conn;
}

function agregar_producto($nombre_producto, $valor_producto,$imagen_producto){
    $conn = conectarBD();
    $sql = "INSERT INTO productos (nombre, valor_producto, imagen_producto) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("sds", $nombre_producto, $valor_producto,$imagen_producto);

    $resultado=$stmt->execute();

    $stmt->close();
    $conn->close();
    
    return $resultado;
}

function modificar_producto($id, $nuevo_nombre, $nuevo_valor){
    $conn = conectarBD();
    $sql = "UPDATE productos SET nombre = ?, valor_producto = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error al preparar: " . $conn->error);
    }

    $stmt->bind_param("sdi", $nuevo_nombre, $nuevo_valor, $id);

    if ($stmt->execute()) {
        echo "<script>alert('\u2705 Producto modificado correctamente');</script>";
    } else {
        echo "<script>alert('\u274c Error al modificar: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}

function obtener_producto_por_nombre($nombre) {
    $conn = conectarBD();
    $sql = "SELECT id, nombre, valor_producto,imagen_producto FROM productos WHERE TRIM(nombre) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $producto = $resultado->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $producto;
}

// function eliminar_producto($id, $eliminado_nombre, $eliminado_valor){
//     $conn = conectarBD();
//     $sql = "UPDATE productos SET nombre = ?, valor_producto = ? WHERE id = ?";
//     $stmt = $conn->prepare($sql);

//     if (!$stmt) {
//         die("Error al preparar: " . $conn->error);
//     }

//     $stmt->bind_param("sdi", $nuevo_nombre, $nuevo_valor, $id);

//     if ($stmt->execute()) {
//         echo "<script>alert('\u2705 Producto modificado correctamente');</script>";
//     } else {
//         echo "<script>alert('\u274c Error al modificar: " . $stmt->error . "');</script>";
//     }

//     $stmt->close();
//     $conn->close();
// }


function cotizar_productos($cant_prod_cotizar, $val_prod_cotizar, $val_diseño_cotizar){
    $conn = conectarBD();

    // Calcula total
    $total = ($cant_prod_cotizar * $val_prod_cotizar) + $val_diseño_cotizar;

    // Fecha actual y usuario en sesión
    $fecha = date("Y-m-d");
    $id_usuario = $_SESSION['administrador']; // Asegúrate que esta sea la clave correcta

    $sql = "INSERT INTO cotizaciones (fecha, id_usuario, total) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("sid", $fecha, $id_usuario, $total);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Cotización registrada correctamente');</script>";
    } else {
        echo "<script>alert('❌ Error al registrar la cotización: " . $stmt->error . "');</script>";
    }

    $stmt->close();
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
    <script
    src="https://kit.fontawesome.com/81581fb069.js"
    crossorigin="anonymous"
></script>
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
            <button onclick="buscarProductoParaCotizar()">Cotizar</button>
            <a href="#" onclick="event.preventDefault(); abrirModalP1();">
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
                    
                    <button onclick="buscarProductoParaCotizar()">Cotizar</button>
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
                <form method="POST" class="formulario-P2" id="formularioCotizarP2" enctype="multipart/form-data">
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
                    <!-- Es buena idea incluir un campo oculto para enviar el valor del producto -->
                    <input type="hidden" name="valor_producto_cotizar" id="valorProductoOcultoP2" value="">
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
                    <button type="submit">Cotizar</button>
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