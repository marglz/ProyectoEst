<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: login.html");
    exit;
}
$usuario = $_SESSION['usuario'];

$json_file = "output/estado_cajones.json";
$estados = [];
if (file_exists($json_file)) {
    $contenido = file_get_contents($json_file);
    $estados = json_decode($contenido);
}

$conexion = new mysqli("localhost", "root", "", "bite");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cajon_id'])) {
    $cajon = intval($_POST['cajon_id']);

    // Verificar si ya está reservado
    $verificar = $conexion->query("SELECT * FROM reservaciones WHERE cajon = $cajon AND DATE(fecha) = CURDATE()");
    if ($verificar->num_rows == 0) {
        $conexion->query("INSERT INTO reservaciones (usuario, cajon) VALUES ('$usuario', $cajon)");
        echo "<script>alert('¡Cajón $cajon reservado correctamente!');</script>";
    } else {
        echo "<script>alert('Este cajón ya fue reservado.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página Principal</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    
<?php if ($_SESSION['rol'] === 'admin'): ?>
    <a href="admin_dashboard.php">Administración</a>
<?php endif; ?>
    
        <!-- Barra de navegación -->
    <div class="navbar">
        <h2>Estacionamiento Digital</h2>
        <div class="nav-links">
            <a href="principal.php">Inicio</a>
            <a href="resenas.php">Reseñas</a>
            <a href="admin_dashboard.php">Administración</a>
            <a href="logout.php">Cerrar sesión</a>
        </div>
    </div>

    <h1>
        Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?>
        <div class="user-info">
            <div class="user-icon">👤</div>
        </div>
    </h1>

    <p class="inicio">¿Necesitas un cajón para estacionarte?</p>
    <button class="boton-buscar" onclick="buscarEstacionamientos()">Buscar estacionamientos</button>
    <div id="map" style="height: 400px; display: none;"></div>
    <div id="lista-estacionamientos"></div>

    <?php

    $json_file = "output/estado_cajones.json";
$estados = [];

if (file_exists($json_file)) {
    $contenido = file_get_contents($json_file);
    $estados = json_decode($contenido);
}
?>

    <h2>Disponibilidad de Cajones</h2>
    <?php if (!empty($estados)): ?>
<form method="POST">
    <div class="contenedor-cajones">
        <?php foreach ($estados as $i => $estado): ?>
            <?php
                $res = $conexion->query("SELECT * FROM reservaciones WHERE cajon = $i AND DATE(fecha) = CURDATE()");
                $reservado = $res->num_rows > 0;
                $ocupado = $estado == 1 || $reservado;
                $clase = $ocupado ? 'ocupado' : 'libre';
                $texto = $ocupado ? 'Ocupado' : 'Reservar';
            ?>
            <button 
                type="<?= $ocupado ? 'button' : 'submit' ?>" 
                name="cajon_id" 
                value="<?= $i ?>" 
                class="cajon <?= $clase ?>" 
                data-label="Cajón <?= $i + 1 ?>"
                <?= $ocupado ? 'disabled' : '' ?>>
                <?= $texto ?>
            </button>
        <?php endforeach; ?>
    </div>
</form>
<?php else: ?>
    <p style="text-align:center; font-weight:bold; color: #e67e22;">No hay datos de disponibilidad.</p>
<?php endif; ?>

<div style="text-align: center; margin-top: 40px;">
    <h3>Ultima Vista de Cajon </h3>
    <img src="output/frame.jpg?<?= time() ?>" alt="Detección actual" style="max-width: 600px; border-radius: 12px; border: 2px solid #bdc3c7;">
</div>

    <!-- Google Maps Script -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAQ-RUr30cGYX0a2T1u_a2Hsn20xSPD8fI&libraries=places"
        async
        defer>
    </script>

    <!-- Script para mostrar estacionamientos cercanos -->
    <script>
        let map;
        let userLocation;

        function buscarEstacionamientos() {
            document.getElementById('map').style.display = 'block';
            document.getElementById('lista-estacionamientos').innerHTML = "";

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    map = new google.maps.Map(document.getElementById('map'), {
                        center: userLocation,
                        zoom: 15
                    });

                    new google.maps.Marker({
                        position: userLocation,
                        map,
                        title: "Tu ubicación"
                    });

                    const service = new google.maps.places.PlacesService(map);
                    service.textSearch({
                        location: userLocation,
                        radius: 1000,
                        query: 'estacionamiento'
                    }, function(results, status) {
                        if (status === google.maps.places.PlacesServiceStatus.OK) {
                            const lista = document.createElement("ul");
                            lista.style.listStyle = "none";
                            lista.style.padding = "0";

                            results.forEach(place => {
                                new google.maps.Marker({
                                    position: place.geometry.location,
                                    map,
                                    title: place.name
                                });

                                const item = document.createElement("li");
                                item.innerHTML = `<strong>${place.name}</strong><br><small>${place.formatted_address || place.vicinity}</small><hr>`;
                                lista.appendChild(item);
                            });

                            document.getElementById('lista-estacionamientos').appendChild(lista);
                        } else {
                            console.error("Error en textSearch:", status);
                            alert("No se encontraron estacionamientos: " + status);
                        }
                    });
                }, function() {
                    alert("No se pudo obtener tu ubicación.");
                });
            } else {
                alert("Tu navegador no soporta geolocalización.");
            }
        }
    </script>

    <script src="principal_script.js"></script>

</body>

</html>