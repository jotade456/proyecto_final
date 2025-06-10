<?php
session_start();
header('Content-Type: application/json');


if (!isset($_SESSION['administrador'])) {
    echo json_encode(['success' => false, 'error' => 'No autorizado']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "cotizacionesmagicas");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión']);
    exit;
}


if (isset($_POST['eliminar_usuario'])) {
    $id_usuario = intval($_POST['eliminar_usuario']);

    $stmt = $conn->prepare("UPDATE usuarios SET estado = 'inactivo' WHERE id = ?");
    $stmt->bind_param("i", $id_usuario);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Usuario inhabilitado']);
        header("Location: login.php");
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al actualizar']);
    }

    $stmt->close();
    $conn->close();
    exit;
}

$sql = "SELECT id, nombre_usuario, correo, rol FROM usuarios WHERE estado = 'activo'";
$result = $conn->query($sql);

$usuarios = [];

while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}

$conn->close();

echo json_encode(['success' => true, 'usuarios' => $usuarios]);
