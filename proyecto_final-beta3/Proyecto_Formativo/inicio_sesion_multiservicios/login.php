<?php
function conectarBD()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "multiserviciosroma";

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

        $stmt = $conn->prepare("SELECT id, nombre_usuario, contraseña, rol FROM usuarios WHERE nombre_usuario = ? AND estado = 'activo'");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {
            $usuarioRol = $resultado->fetch_assoc();

            // verifica contraseña
            if (password_verify($clave, $usuarioRol['contraseña'])) {
                session_start();
                $_SESSION['id_usuario'] = $usuarioRol['id'];
                $_SESSION['rol'] = $usuarioRol['rol'];

                if ($usuarioRol['rol'] == 'administrador') {
                    $_SESSION['administrador'] = $usuarioRol['nombre_usuario'];
                    header('Location: ../ADMIN/animacion.php');
                    exit();
                } elseif ($usuarioRol['rol'] == 'empleado') {
                    $_SESSION['empleado'] = $usuarioRol['nombre_usuario'];
                    header('Location: ../ADMIN/animacion.php');
                    exit();
                } else {
                    echo "<script>alert('Rol no reconocido'); window.history.back();</script>";
                    exit();
                }

            } else {
                echo "<script>alert('Contraseña incorrecta'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Usuario no encontrado'); window.history.back();</script>";
        }

        $stmt->close();
        $conn->close();
    }
}



iniciar_sesion();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="styleLogin.css">
    <link rel="icon" type="image/png" sizes="32x32" href="imagenes/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="imagenes/favicon-16x16.png">
    <link rel="shortcut icon" href="imagenes/favicon.ico">
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

            <a href="recuperar.php" class="forgot-password">Recuperar contraseña</a>

            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>

    </div>

</body>
</html>
