<?php
session_start();
if (!isset($_SESSION['empleado'])) {
    header("Location: ../inicio_sesion_multiservicios/login.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multiservicios Roma</title>
    <link rel="stylesheet" href="estilos2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
    src="https://kit.fontawesome.com/81581fb069.js"
    crossorigin="anonymous"
    </script>
    <!-- FAVICON -->
    <link rel="icon" type="image/png" sizes="32x32" href="imagenes/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="imagenes/favicon-16x16.png">
    <link rel="shortcut icon" href="imagenes/favicon.ico">

</head>

<body>
    <header class="header__container">
        <nav class="navbar">
            <section class="waveNavbar">
                <div class="wavenav wave1nav"></div>
                <div class="wavenav wave2nav"></div>
                <div class="wavenav wave3nav"></div>
                <div class="wavenav wave4nav"></div>
            </section>
            <div class="logo">
                <img src="../imagenes/LogoRoma2.png" alt="Logo Roma">
            </div>
            <div class="action">
                <div class="profile" onclick="menuToggle()">
                    <img id="imgPerfilNav" src="../imagenes/1.jpg" alt="Foto de perfil del empleado">
                </div>
                <div class="menu">
                    <h3>
                        <span id="nombrePerfil" class="nombre-usuario">Nombre</span><br>
                        <span id="rolPerfil" class="rol-usuario">Rol</span>
                    </h3>
                    <ul>
                        <li><img src="../imagenes/envelope.png" alt=""><a href="#" onclick="abrirModalHistorial()">Ver Historial</a></li>
                        <li><img src="../imagenes/question.png" alt=""><a href="#" onclick="abrirModalPerfil(); cargarDatosPerfil();">Editar Perfil</a></li>
                        <li><img src="../imagenes/log-out.png" alt=""><a href="../inicio_sesion_multiservicios/logout.php">Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="welcome-message">
            <h1>Bienvenido, <span id="bienvenidaNombre" class="nombre-bienvenida">Usuario</span></h1>
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
                    <div class="imagen-historial" id="imagenHistorial">
                        <!-- Aquí podrías colocar una imagen opcional si quisieras -->
                    </div>
                    <!-- 🚀 AQUÍ va directamente el historial-contenedor con id="historial" -->
                    <div class="historial-contenedor" id="historial">
                        <!-- Las tarjetas-cotizacion se insertan aquí por JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>






    <!-------  MODAL EDITAR PERFIL ------>  

    <div class="modal-perfil" id="modalPerfil">
        <div class="modal-content-perfil">
            <div class="modal-header-perfil">
                <span class="back-arrow-perfil" onclick="cerrarModalPerfil()">&larr;</span>
                <h2>Editar Perfil</h2>
            </div>
            <div class="modal-body-perfil">
                <div class="perfil-img-container">
                    <label for="imagenPerfil" class="img-label">
                        <img src="https://cdn-icons-png.flaticon.com/512/2922/2922510.png" alt="Perfil" class="perfil-img" />
                        <input type="file" id="imagenPerfil" accept="image/*" hidden />
                    </label>
                </div>

                <!-- ✅ Formulario preparado para trabajar con JS + fetch -->
                <form class="formulario-perfil" method="POST" action="" id="formularioPerfil" enctype="multipart/form-data">
                    <div class="campo">
                        <label for="nombre">Nombre de perfil</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Nuevo nombre">
                    </div>
                    <div class="campo">
                        <label for="correo">Correo electrónico</label>
                        <input type="email" id="correo" name="correo" placeholder="Nuevo correo">
                    </div>
                    <div class="campo">
                        <label for="contraseña">Contraseña</label>
                        <input type="password" id="contraseña" name="contrasena" placeholder="Nueva contraseña">
                    </div>

                    <!-- ✅ El botón es type="button", así el form NO hace submit clásico -->
                    <button type="button" class="btn-perfil">Aplicar cambios</button>
                </form>
            </div>
        </div>
    </div>


    <section>
        <div class="wave wave1"></div>
        <div class="wave wave2"></div>
        <div class="wave wave3"></div>
        <div class="wave wave4"></div>
    </section>

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
    <script>
        document.addEventListener("DOMContentLoaded", () => {
        fetch("../controlador/controlador.php", {
            method: "POST",
            headers: {
            "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "obtener_datos_usuario=1"
        })
        .then(res => res.json())
        .then(data => {
            if (!data.error) {
            // Insertar los datos en la interfaz
            document.getElementById("bienvenidaNombre").textContent = data.nombre_usuario;
            document.getElementById("nombrePerfil").textContent = data.nombre_usuario;
            document.getElementById("rolPerfil").textContent = data.rol;
            } else {
            console.warn("⚠️ Usuario no encontrado:", data.error);
            }
        })
        .catch(err => {
            console.error("❌ Error al obtener datos del usuario:", err);
        });
        });
    </script>

</body>
</html>