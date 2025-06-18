<?php
session_start();
error_log('ID usuario en sesión: ' . (isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 'NO DEFINIDO'));


if (!isset($_SESSION['administrador'])) {
    header("Location: ../inicio_sesion_multiservicios/login.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multiservicios Roma</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
                        <li><img src="../imagenes/editarPerfil.png" alt=""><a href="#" onclick="abrirModalPerfil(); cargarDatosPerfil();">Editar Perfil</a></li>
                        <li><img src="../imagenes/agregarUsuario.png" alt=""><a href="../inicio_sesion_multiservicios/registro.php">Registrar Empleado</a></li>
                        <li><img src="../imagenes/log-out.png" alt=""><a href="../inicio_sesion_multiservicios/logout.php">Cerrar Sesión</a></li>
                        
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="welcome-message">
        <h1>Bienvenido, <span id="bienvenidaNombre" class="nombre-bienvenida">Usuario</span></h1>
    </div>

    <div class="contenedor-principal">
        <a href="manejo_productos.html" class="card">
            <i class="fas fa-box"></i>
            <p>Productos</p>
        </a>
        <a href="#" class="card">
            <i class="fas fa-file-invoice-dollar"></i>
            <p>Cotizaciones</p>
        </a>
        <a href="cotizacion.html" class="card">
            <i class="fas fa-users-cog"></i>
            <p>Administrar Usuarios</p>
        </a>
    </div>

    <section>
        <div class="wave wave1"></div>
        <div class="wave wave2"></div>
        <div class="wave wave3"></div>
        <div class="wave wave4"></div>
    </section>

    <!-- Scripts -->
    <script src="main.js"></script>
    <script src="modal.js"></script>
    <script src="modal_cuentas.js"></script>
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
