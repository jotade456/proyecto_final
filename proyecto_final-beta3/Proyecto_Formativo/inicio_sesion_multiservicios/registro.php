<?php
function conectarBD()
{
    $conn = new mysqli("localhost", "root", "", "multiserviciosroma");

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    return $conn;
}

function registrarUsuario($nombre_usuario, $correo, $password, $rol)
{
    $conn = conectarBD();
    
    if ($rol === 'administrador') {
        $adminCheck = $conn->query("SELECT id FROM usuarios WHERE rol = 'administrador' LIMIT 1");
        if ($adminCheck->num_rows > 0) {
            echo "<script>alert('⚠️ Ya existe un administrador registrado. Solo se permite uno.'); window.history.back();</script>";
            $conn->close();
            return;
        }
    }

    //  Validar si el correo ya está registrado
    $check = $conn->prepare("SELECT id FROM usuarios WHERE correo = ? AND estado = 'activo' ");
    $check->bind_param("s", $correo);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('⚠️ El correo ya está registrado. Usa uno diferente.'); window.history.back();</script>";
        $check->close();
        $conn->close();
        return;
    }
    $check->close();
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nombre_usuario, correo, contraseña, rol) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error al preparar la consulta: " . $conn->error);
    }

    $stmt->bind_param("ssss", $nombre_usuario, $correo, $password_hash, $rol);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Registro exitoso'); window.location.href='../ADMIN/index.php';</script>";
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
    } elseif (strlen($password) < 8) {
        echo "<script>alert('⚠️ La contraseña debe tener al menos 8 caracteres.'); window.history.back();</script>";
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
        
    </div>

</body>
</html>
