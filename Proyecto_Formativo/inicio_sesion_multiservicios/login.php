<?php
function conectarBD()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "CotizacionesMagicas";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    return $conn;
}

function iniciar_sesion()
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $usuario = $_POST["username"];
        $clave = $_POST["password"];

        $conn = conectarBD();

        // ❌ Corregida comilla faltante al final de la consulta SQL
        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$usuario'";
        $resultado = $conn->query($sql);

        if ($resultado->num_rows == 1) {
            $usuarioRol = $resultado->fetch_assoc();

            // ✅ Verificamos la contraseña encriptada
            if (password_verify($clave, $usuarioRol['contraseña'])) {
                session_start();
                $_SESSION['administrador'] = $usuarioRol['nombre_usuario']; // crea la sesión correctamente

                if ($usuarioRol['rol'] == 'administrador') {
                    header('Location: ../ADMIN/index.php');
                    exit();
                } else {
                    header('Location: ../empleado/inicio_empleado.php');
                    exit();
                }

            } else {
                echo "<script>alert('Contraseña incorrecta'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Usuario no encontrado'); window.history.back();</script>";
        }

        $conn->close();
    }
}

// ✅ Ejecutar la función si se envió el formulario
iniciar_sesion();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="styleLogin.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="imagenes/Logo_Roma.png" alt="Logo Multiservicios Roma">
        </div>
        <h2>Iniciar Sesión</h2>

        <form method="POST" id="login-form">
            <label for="username">Nombre de Usuario</label>
            <input type="text" id="username" name="username" placeholder="Nombre" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="Contraseña" required>

            <a href="#" class="forgot-password">Recuperar contraseña</a>

            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>

        <hr>
        <p>¿No tienes cuenta? <a href="registro.php" class="link">Registrarse</a></p>
    </div>

    <script src="script.js"></script>
</body>
</html>
