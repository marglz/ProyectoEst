<?php
// eliminar_usuario.php
$conexion = new mysqli("localhost", "root", "", "bite");

$id = $_GET['id'];

$stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: admin_dashboard.php");
exit();
?>
