<?php
session_start();
error_log('ID usuario en sesión: ' . (isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 'NO DEFINIDO'));


if (!isset($_SESSION['administrador'])) {
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
                    <img id="imgPerfilNav" src="../imagenes/1.jpg" alt="Foto de perfil del administrador">
                </div>
                <div class="menu">
                    <h3>
                        <span id="nombrePerfil" class="nombre-usuario">Nombre</span><br>
                        <span id="rolPerfil" class="rol-usuario">Rol</span>
                    </h3>
                    <ul>
                        <li><img src="../imagenes/user.png" alt=""><a href="#" onclick="abrirModal()">Agregar Producto</a></li>
                        <li><img src="../imagenes/edit.png" alt=""><a href="#" onclick="abrirModalModificar()">Modificar Producto</a></li>
                        <li><img src="../imagenes/settings.png" alt=""><a href="#" onclick="abrirModalEliminar()">Eliminar Producto</a></li>
                        <li><img src="../imagenes/envelope.png" alt=""><a href="#" onclick="abrirModalHistorial()">Ver Historial</a></li>
                        <li><img src="../imagenes/question.png" alt=""><a href="#" onclick="abrirModalCuentas()">Cuentas</a></li>
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
        <!-- Ventana emergente para errores -->
        <div class="ventana-emergente-busq" id="ventanaEmergenteError">
            <div class="ventana-contenido-busq">
                <div class="ventana-encabezado-busq">
                    <h2>⚠ Producto no encontrado</h2>
                </div>
                <div class="ventana-cuerpo">
                    <p>El producto que buscaste no existe o no está activo.</p>
                </div>
            </div>
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
                <form id="formAgregarProducto" class="formulario-producto" enctype="multipart/form-data">
                    
                    <div class="contenedor-formulario">

                        <div class="imagen-box" onclick="document.getElementById('inputImagen').click();">
                            <input name="imagen_producto" type="file" id="inputImagen" onchange="mostrarImagen(event)">
                            <img id="previewImagen" style="display: none;">
                            <p>Haz clic para subir imagen</p>
                        </div>

                        <div class="campos-producto">
                            <label for="nombreProductoAgregar">Nombre del Producto</label>
                            <input name="nombre_producto" type="text" id="nombreProductoAgregar" required>
                            <label for="valorProductoAgregar">Valor del Producto</label>
                            <input name="valor_producto" type="number" id="valorProductoAgregar" required>
                            <button type="submit">Agregar</button>
                        </div>
                    </div>
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
                    <form id="formBuscarModificar" onsubmit="return false;"method="POST" action="../controlador/controlador.php">
                        <input type="text" name="buscar_nombre" id="inputBuscarModificar" placeholder="Buscar Producto" required>
                        <button type="submit"><i class="fa-solid fa-magnifying-glass icon"></i></button>
                    </form>
                    <ul class="container-suggestions-modificar"></ul>
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
                <form class="formulario-producto-mod" id="formularioModificarProducto" onsubmit="return false;">
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


    <!-- MODAL ELIMINAR PRODUCTO -->
    <div class="modal-eliminar" id="modaleliminar">
        <div class="modal-content-eliminar">
            <div class="modal-header-eliminar">
                <span class="back-arrow" onclick="cerrarModalEliminar()">&larr;</span>
                <h2>Eliminar Producto</h2>
            </div>
            <div class="modal-body-eliminar">
                <div class="buscador-eliminar">
                    <form id="formBuscarEliminar" onsubmit="return false;">
                        <input type="text" name="buscar_nombre" id="inputBuscarEliminar" placeholder="Buscar Producto" required>
                        <button type="submit"><i class="fa-solid fa-magnifying-glass icon"></i></button>
                    </form>
                    <ul class="contenedor-sugerencias-eliminar" id="contenedorSugerenciasEliminar"></ul>
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
                <div class="imagen-box-eli">
                    <input type="file" id="inputImagen-eli" disabled onchange="mostrarImagenEli(event)">
                    <img id="previewImagen-eli" style="display: none;">
                    <p>Eliminar Imagen</p>
                </div>

                <form id="formularioEliminarProducto" class="formulario-producto-eli" onsubmit="return false;">
                    <input type="hidden" name="eliminar_producto" id="idProducto-eli">
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

                    <input type="hidden" id="idProductoP1" value="">

                    <input type="hidden" id="imagenProductoP1" value="">

                    <button type="button">Cotizar</button>
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

                <div class="imagen-boxP2">
                    <img id="previewImagenP2" style="display: none;">
                </div>

                <form method="POST" class="formulario-P2" id="formularioCotizarP2" enctype="multipart/form-data" onsubmit="return false;">
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

                    <input type="hidden" name="valor_producto_cotizar" id="valorProductoOcultoP2">
                    <input type="hidden" name="id_producto" id="idProductoOcultoP2">

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

                    <button type="button" onclick="cerrarModalP3()">Cerrar</button>
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
                    </div>
                    <div class="historial-contenedor" id="historial">
                    </div>
                </div>
            </div>
        </div>
    </div>


    
    <!-------  MODAL ADMINISTRACION DE CUENTAS ------>
    <div class="modal-cuentas" id="modalCuentas">
        <div class="modal-content-cuentas">
            <div class="modal-header-cuentas">
                <span class="back-arrow-cuentas" onclick="cerrarModalCuentas()">&larr;</span>
                <h2>Cuentas</h2>
            </div>
            <div class="modal-body-cuentas">
                <div class="usuarios-contenedor" id="usuariosContenedor">
                </div>
                <hr class="linea-separadora">
                <button class="boton-eliminar">Eliminar</button>
                <div class="ventana-emergente-cuentas" id="ventanaEmergenteCuentas" style="display: none;">
                    <div class="ventana-contenido-cuentas">
                        <h2>Eliminar cuenta</h2>
                        <p>Cuenta eliminada con éxito</p>
                    </div>
                </div>
                <div class="ventana-confirmacion-cuentas" id="ventanaConfirmacionCuentas" style="display: none;">
                    <div class="ventana-contenido-confirmacion">
                        <h2>Confirmar eliminación</h2>
                        <p>¿Estás seguro de que deseas inhabilitar esta cuenta?</p>
                        <div class="botones-confirmacion">
                            <button id="btnConfirmarEliminarCuenta">Aceptar</button>
                            <button id="btnCancelarEliminarCuenta">Cancelar</button>
                        </div>
                    </div>
                </div>
                <div class="ventana-seleccion-cuentas" id="ventanaSeleccionCuentas" style="display: none;">
                    <div class="ventana-contenido-seleccion">
                        <h2>Advertencia</h2>
                        <p>Por favor selecciona un usuario para eliminar.</p>
                        <button id="btnCerrarSeleccionCuentas">OK</button>
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

                    <button type="button" class="btn-perfil">Aplicar cambios</button>
                </form>
                <div class="ventana-emergente-perfil" id="ventanaEmergentePerfil">
                    <div class="ventana-contenido-perfil">
                        <div class="ventana-encabezado-perfil">
                            <h2>Perfil</h2>
                        </div>
                        <div class="ventana-cuerpo-perfil">
                            <p>Perfil actualizado correctamente</p>
                        </div>
                    </div>
                </div>
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
    <script src="modal_cuentas.js"></script>
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