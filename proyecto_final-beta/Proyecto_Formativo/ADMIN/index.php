<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 🔁 NUEVO: para peticiones AJAX desde JavaScript (buscador con fetch)
    if (isset($_POST['ajax_buscar_nombre'])) {
        header('Content-Type: application/json');
        $nombre = trim($_POST['ajax_buscar_nombre']);
        $producto = obtener_producto_por_nombre($nombre);
        echo json_encode($producto ?: ['error' => 'Producto no encontrado']);
        exit;
    }

    // ✅ Registro desde formulario tradicional
    if (isset($_POST['nombre_producto']) && isset($_POST['valor_producto'])) {
        agregar_producto($_POST['nombre_producto'], $_POST['valor_producto']);
    }

    // ✅ Modificación desde formulario tradicional
    if (isset($_POST['id_producto']) && isset($_POST['nuevo_nombre']) && isset($_POST['nuevo_valor'])) {
        modificar_producto($_POST['id_producto'], $_POST['nuevo_nombre'], $_POST['nuevo_valor']);
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

function agregar_producto($nombre_producto, $valor_producto){
    $conn = conectarBD();
    $sql = "INSERT INTO productos (nombre, precio_unitario) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("sd", $nombre_producto, $valor_producto);

    if ($stmt->execute()) {
        echo "<script>alert('\u2705 Registro exitoso');</script>";
    } else {
        echo "<script>alert('\u274c Error al registrar: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}

function modificar_producto($id, $nuevo_nombre, $nuevo_valor){
    $conn = conectarBD();
    $sql = "UPDATE productos SET nombre = ?, precio_unitario = ? WHERE id = ?";
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
    $sql = "SELECT id, nombre, precio_unitario FROM productos WHERE TRIM(nombre) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $producto = $resultado->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $producto;
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
                        <li><img src="../imagenes/settings.png" alt=""><a href="#">Eliminar Producto</a></li>
                        <li><img src="../imagenes/envelope.png" alt=""><a href="#">Ver Historial</a></li>
                        <li><img src="../imagenes/question.png" alt=""><a href="#">Editar Perfil</a></li>
                        <li><img src="../imagenes/log-out.png" alt=""><a href="#">Cerrar Sesión</a></li>
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
            <input type="text" placeholder="Buscar Producto" />
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
                <div class="imagen-box" onclick="document.getElementById('inputImagen').click();">
                    <input type="file" id="inputImagen" onchange="mostrarImagen(event)">
                    <img id="previewImagen" style="display: none;">
                    <p>Haz clic para subir imagen</p>
                </div>
                <form method="POST" action="index.php" class="formulario-producto">
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


    <!--MODAL MODIFICAR PRODUCTO-->
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



    <!-------  MODAL PÁGINA PRINCIPAL 1 ------>
    <div class="modal-principal1" id="modalPrincipal1">
        <div class="modal-contentP1">
            <div class="modal-headerP1">
                <span class="back-arrow" onclick="cerrarModalP1()">&larr;</span>
            </div>
            <div class="modal-bodyP1">
                <div class="imagen-boxP1">
                    <input type="file" id="inputImagen" onchange="mostrarImagenP1(event)">
                    <img id="previewImagenP1" style="display: none;">                
                </div>
                <form class="formulario-P1">
                    <div class="grupo-nombrep1">
                        <label for="nombreProducto">Nombre del Producto</label>
                        <input type="text" id="nombreProducto" placeholder="" readonly>
                    </div>     
                    <div class="grupo-valorp1">
                        <label for="valorProducto">Valor del Producto</label>
                        <input type="number" id="valorProducto" placeholder="" readonly>
                    </div> 
                    <button type="submit">Cotizar</button>
                </form>
            </div>
        </div>
    </div>


    <!-------  MODAL PÁGINA PRINCIPAL 2 ------>
    <div class="modal-principal2" id="modalPrincipal2">
        <div class="modal-contentP2">
            <div class="modal-headerP2">
                <span class="back-arrow" onclick="cerrarModalP2()">&larr;</span>
            </div>
            <div class="modal-bodyP2">
                <div class="imagen-boxP2">
                    <input type="file" id="inputImagen" onchange="mostrarImagenP2(event)">
                    <img id="previewImagenP2" style="display: none;">                
                </div>
                <form class="formulario-P2">
                    <div class="grupo-cantidadP2">
                        <label for="cantidadProducto">Cantidad del Producto</label>
                        <input type="number" id="cantidadProducto" placeholder="Cantidad" required>
                    </div>     
                    <div class="grupo-valorImpresionP2">
                        <label for="valorProducto">Valor del Impresión</label>
                        <input type="number" id="valorImpresion" placeholder="$$$" required>
                    </div>
                    <div class="grupo-valorDiseñoP2">
                        <label for="valorDiseño">Valor del Diseño</label>
                        <input type="number" id="valorDiseño" placeholder="$$$" required>
                    </div>
                    <button type="submit">Cotizar</button>
                </form>
            </div>
        </div>
    </div>


    <!-------  MODAL PÁGINA PRINCIPAL 3 ------>
    <div class="modal-principal3" id="modalPrincipal3">
        <div class="modal-contentP3">
            <div class="modal-headerP3">
                <span class="back-arrow" onclick="cerrarModalP3()">&larr;</span>
            </div>
            <div class="modal-bodyP3">
                <div class="imagen-boxP3">
                    <input type="file" id="inputImagen" onchange="mostrarImagenP3(event)">
                    <img id="previewImagenP3" style="display: none;">                
                </div>
                <form class="formulario-P3">
                    <div class="grupo-nombreP3">
                        <label for="nombreProducto">Nombre del Producto</label>
                        <input type="text" id="nombreProducto" placeholder="" readonly>
                    </div>     
                    <div class="grupo-totalP3">
                        <label for="precioTotal">Precio Total</label>
                        <input type="number" id="precioTotal" placeholder="" readonly>
                    </div> 
                    <button type="submit">Cotizar</button>
                </form>
                <div class="ventana-emergenteP3" id="ventanaEmergenteP3">
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
    <script src="suggestions.js"></script>
    <script src="main.js"></script>
    <script src="modal.js"></script>
</body>
</html>