<?php
function conectarBD() {
    $conn = new mysqli("localhost", "root", "", "cotizacionesmagicas");
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    return $conn;
}

function mostrar_historial() {
    $conn = conectarBD();

    $query = "SELECT c.id, c.id_usuario, c.fecha, d.id_producto, d.cantidad, d.precio_unitario, d.subtotal, c.total as total_cotizacion, p.nombre as nombre_producto
                FROM cotizaciones c
                INNER JOIN detalle_cotizacion d ON c.id = d.id_cotizacion
                INNER JOIN productos p ON d.id_producto = p.id
                ORDER BY c.fecha DESC";

    $result = $conn->query($query);

    $historial = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id_cotizacion = $row['id'];

            if (!isset($historial[$id_cotizacion])) {
                $historial[$id_cotizacion] = [
                    'id' => $row['id'],
                    'id_usuario' => $row['id_usuario'],
                    'fecha' => $row['fecha'],
                    'total_cotizacion' => $row['total_cotizacion'],
                    'nombre_producto' => $row['nombre_producto'],
                    'cantidad' => $row['cantidad'],
                    'precio_unitario' => $row['precio_unitario'],
                    'subtotal' => $row['subtotal']
                ];
            }
        }
    }

    $conn->close();

    header('Content-Type: application/json');
    echo json_encode(array_values($historial));
}

mostrar_historial();
?>


