<?php
$conexion = new mysqli("localhost", "root", "", "bite");
$res = $conexion->query("SELECT cajon FROM reservaciones WHERE DATE(fecha) = CURDATE()");
$reservados = [];
while ($row = $res->fetch_assoc()) {
    $reservados[] = (int)$row['cajon'];
}
header('Content-Type: application/json');
echo json_encode($reservados);
?>
