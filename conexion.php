<?php
$conexion = new mysqli("localhost", "root", "", "bite");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>