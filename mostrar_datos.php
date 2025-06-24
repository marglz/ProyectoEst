<?php
include("conexion.php");

$resultado = $conexion->query("SELECT * FROM usuarios");

echo "<h1>Usuarios registrados:</h1>";
while ($fila = $resultado->fetch_assoc()) {
    echo "ID: " . $fila["id"] . " - Nombre: " . $fila["nombre"] . " - matricula: " . $fila["matricula"] . "<br>";
}
?>
