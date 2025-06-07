<?php
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';
require 'config_mail.php'; // Importa configuración

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = trim($_POST['correo']);

    // Conexión y verificación del correo
    $conn = new mysqli("localhost", "root", "", "cotizacionesmagicas");

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $res = $stmt->get_result();
    $usuario = $res->fetch_assoc();

    if ($usuario) {
        // Generar nueva contraseña aleatoria
        $nuevaPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10);
        $hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

        // Guardarla en la base de datos
        $stmt2 = $conn->prepare("UPDATE usuarios SET contraseña = ? WHERE id = ?");
        $stmt2->bind_param("si", $hash, $usuario['id']);
        $stmt2->execute();

        // Enviar la nueva contraseña por correo
        $cfg = require 'config_mail.php';
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $cfg['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $cfg['username'];
            $mail->Password   = $cfg['password'];
            $mail->SMTPSecure = 'tls';
            $mail->Port       = $cfg['port'];

            $mail->setFrom($cfg['from_email'], $cfg['from_name']);
            $mail->addAddress($correo);
            $mail->isHTML(true);
            $mail->Subject = 'Nueva contraseña';
            $mail->Body    = "Hola, tu nueva contraseña es: <b>$nuevaPassword</b><br>Por favor cámbiala después de iniciar sesión.";

            $mail->send();
            echo "✅ Se ha enviado una nueva contraseña a tu correo.";
        } catch (Exception $e) {
            echo "❌ Error al enviar correo: {$mail->ErrorInfo}";
        }
    } else {
        echo "❌ No se encontró una cuenta con ese correo.";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="styleRecuperar.css"> 
</head>
<body>
    <div class="container">
        <h2>Recuperar Contraseña</h2>
        <form method="POST">
            <label for="correo">Correo:</label>
            <input type="email" name="correo" id="correo" required>
            <button type="submit">Enviar nueva contraseña</button>
        </form>
        <p><a href="login.php">Volver al login</a></p>
    </div>
</body>
</html>

