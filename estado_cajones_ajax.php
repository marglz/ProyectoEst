<?php
$conexion = new mysqli("localhost", "root", "", "bite");

$json_file = "output/estado_cajones.json";
$estados = file_exists($json_file) ? json_decode(file_get_contents($json_file)) : [];

$resultado = [];
foreach ($estados as $i => $estado) {
    $numero_cajon = $i + 1;
    $res = $conexion->query("SELECT * FROM reservaciones WHERE cajon = $numero_cajon AND DATE(fecha) = CURDATE()");
    $reservado = $res->num_rows > 0;

    if ($reservado) {
        $resultado[] = [
            "numero" => $numero_cajon,
            "estado" => "reservado",
            "texto" => "Reservado"
        ];
    } elseif ($estado == 1) {
        $resultado[] = [
            "numero" => $numero_cajon,
            "estado" => "ocupado",
            "texto" => "Ocupado"
        ];
    } else {
        $resultado[] = [
            "numero" => $numero_cajon,
            "estado" => "libre",
            "texto" => "Reservar"
        ];
    }
}