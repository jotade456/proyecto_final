<?php
require('librerias/fpdf.php');

// --- Conexion a la base de datos ---
$conexion = new mysqli("localhost", "root", "", "multiserviciosroma");
if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}

// --- Obtener ID de cotizacion ---
$cotizacion_id = isset($_GET['cotizacion_id']) ? intval($_GET['cotizacion_id']) : 0;

if ($cotizacion_id <= 0) {
    die("ID de cotizacion invalido");
}

// --- Consultar cotizacion ---
$sql_cotizacion = "SELECT * FROM cotizaciones WHERE id = $cotizacion_id LIMIT 1";
$resultado_cotizacion = $conexion->query($sql_cotizacion);

if ($resultado_cotizacion->num_rows === 0) {
    die("Cotizacion no encontrada");
}

$cotizacion = $resultado_cotizacion->fetch_assoc();

// --- Consultar detalles ---
$sql_detalle = "SELECT * FROM detalle_cotizacion WHERE id_cotizacion = $cotizacion_id";
$resultado_detalle = $conexion->query($sql_detalle);

// --- Crear PDF ---
$pdf = new FPDF();
$pdf->AddPage();

// --- Titulo ---
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Cotizacion N° ' . $cotizacion['id'],0,1,'C');
$pdf->Ln(10);

// --- Datos generales ---
$pdf->SetFont('Arial','',12);
$pdf->Cell(50,10,'Fecha: ' . $cotizacion['fecha'],0,1);
$pdf->Cell(50,10,'Usuario ID: ' . $cotizacion['id_usuario'],0,1);
$pdf->Cell(50,10,'Total: $' . number_format($cotizacion['total'], 2),0,1);
$pdf->Ln(10);

// --- Tabla de productos ---
$pdf->SetFont('Arial','B',12);
$pdf->Cell(20,10,'Cant.',1);
$pdf->Cell(60,10,'Producto ID',1);
$pdf->Cell(40,10,'Precio Unit.',1);
$pdf->Cell(40,10,'Subtotal',1);
$pdf->Ln();

$pdf->SetFont('Arial','',12);
while ($fila = $resultado_detalle->fetch_assoc()) {
    $pdf->Cell(20,10, $fila['cantidad'],1);
    $pdf->Cell(60,10, $fila['id_producto'],1);
    $pdf->Cell(40,10, '$' . number_format($fila['precio_unitario'], 2),1);
    $pdf->Cell(40,10, '$' . number_format($fila['subtotal'], 2),1);
    $pdf->Ln();
}

$pdf->Ln(10);

// --- Total final ---
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'TOTAL COTIZACION: $' . number_format($cotizacion['total'], 2),0,1,'R');

// --- Salida ---
$pdf->Output('I', 'Cotizacion_'.$cotizacion_id.'.pdf');
?>
