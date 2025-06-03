<?php
function conectarBD() {
    $conn = new mysqli("localhost", "root", "", "cotizacionesmagicas");
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    return $conn;
}

function mostrar_historial(){
    $conn = conectarBD();
    $query = "SELECT * FROM historial";
    $result = $conn->query($query);

    $historial = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $historial[] = $row;
        }
    }

    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($historial);
}

mostrar_historial();
?>
