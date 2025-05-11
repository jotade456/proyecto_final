<?php
function conectarBD()
{
    $conn = new mysqli("localhost", "root", "", "multiservicios_roma");

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    return $conn;
}

function registrarUsuario($nombre_usuario, $correo, $password, $rol)
{
    $conn = conectarBD();
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre_usuario, correo, contraseña, rol) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("ssss", $nombre_usuario, $correo, $password_hash, $rol);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Registro exitoso'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('❌ Error al registrar: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre_usuario = $_POST['username'] ?? '';
    $correo = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $rol = $_POST['role'] ?? '';

    if (!$nombre_usuario || !$correo || !$password || !$rol) {
        echo "<script>alert('⚠️ Por favor completa todos los campos.'); window.history.back();</script>";
    } else {
        registrarUsuario($nombre_usuario, $correo, $password, $rol);
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="styleLogin.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="imagenes/Logo_Roma.png" alt="Logo Multiservicios Roma">
        </div>
        <h2>Crear Cuenta</h2>

        <form id="register-form" method="POST" action="registro.php">
                <label for="username">Nombre de Usuario</label>
            <input type="text" id="username" name="username" placeholder="Nombre" required>

            <label for="email">Ingrese Email</label>
            <input type="email" id="email" name="email" placeholder="Email" required>

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="Contraseña" required>

            <label for="role">Elije Rol</label>
            <select id="role" name="role" required>
                <option value="" disabled selected>Selecciona un rol</option>
                <option value="administrador">Administrador</option>
                <option value="empleado">Empleado</option>
            </select>

            <button type="submit" class="btn-registrar">Registrarse</button>
        </form>

        <hr>
        <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar Sesión</a></p>
    </div>

    <script src="script.js"></script>
</body>
</html>
