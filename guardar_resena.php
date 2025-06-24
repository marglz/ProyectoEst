<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_SESSION['usuario'];
    $comentario = $_POST['comentario'];
    $calificacion = intval($_POST['calificacion']);

    $conn = new mysqli("localhost", "root", "", "bite");
    if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

    $stmt = $conn->prepare("INSERT INTO reseñas (usuario, comentario, calificacion) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $usuario, $comentario, $calificacion);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: principal.php");  
}
?>
