<?php
session_start();

$conexion = new mysqli("localhost", "root", "", "bite", 3306);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$nombre = strtolower(trim($_POST['nombre'] ?? ''));
$matricula = trim($_POST['matricula'] ?? '');

// Validación rápida de campos vacíos
if (empty($nombre) || empty($matricula)) {
    echo "error";
    exit;
}

$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE LOWER(nombre) = ? AND matricula = ?");
$stmt->bind_param("ss", $nombre, $matricula);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado && $resultado->num_rows > 0) {
    $usuario = $resultado->fetch_assoc();
    $_SESSION['usuario'] = $usuario['nombre'];
    $_SESSION['rol'] = $usuario['rol'];

    echo $usuario['rol']; // admin o usuario
} else {
    echo "error";
}

$stmt->close();
$conexion->close();

?>
