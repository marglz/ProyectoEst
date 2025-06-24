<?php
$conexion = new mysqli("localhost", "root", "", "bite");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$nombre = $_POST['nombre'];
$matricula = $_POST['matricula'];

// Verificar si ya existe la matrícula
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE matricula = ?");
$stmt->bind_param("s", $matricula);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    echo "La matrícula ya está registrada.";
} else {
    // Insertar nuevo usuario
    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, matricula) VALUES (?, ?)");
    $stmt->bind_param("ss", $nombre, $matricula);
    if ($stmt->execute()) {
        echo "Registro exitoso.";
    } else {
        echo "Error al registrar: " . $conexion->error;
    }
}

$stmt->close();
$conexion->close();
?>
