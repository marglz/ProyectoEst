<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'usuario') {
    header("Location: login.html");
    exit;
}
$usuario = $_SESSION['usuario'];

$conexion = new mysqli("localhost", "root", "", "bite");

// Manejar reservación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cajon_id'])) {
    $cajon = intval($_POST['cajon_id']); 

    // Verificar si ya está reservado para hoy
    $verificar = $conexion->query("SELECT * FROM reservaciones WHERE cajon = $cajon AND DATE(fecha) = CURDATE()");
    if ($verificar->num_rows == 0) {
        $conexion->query("INSERT INTO reservaciones (usuario, cajon, fecha) VALUES ('$usuario', $cajon, NOW())");
        echo "<script>alert('¡Cajón $cajon reservado correctamente!'); window.location.href='principal.php';</script>";
        exit;
    } else {
        echo "<script>alert('Este cajón ya fue reservado.'); window.location.href='principal.php';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página Principal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .cajon.ocupado { background-color: #9b59b6; color: white; }
        .cajon.libre { background-color: #2ecc71; color: white; }
        .cajon.reservado { background-color: #e67e22; color: white; }
    </style>
</head>

<body>
<div class="navbar">
    <h2>Estacionamiento Digital</h2>
    <div class="nav-links">
        <a href="principal.php">Inicio</a>
        <a href="resenas.php">Reseñas</a>
        <?php if ($_SESSION['rol'] === 'admin'): ?>
            <a href="admin_dashboard.php">Administración</a>
        <?php endif; ?>
        <a href="logout.php">Cerrar sesión</a>
    </div>
</div>

<h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>

<p class="inicio">¿Necesitas un cajón para estacionarte?</p>
<button class="boton-buscar" onclick="buscarEstacionamientos()">Buscar estacionamientos</button>
<div id="map" style="height: 400px; display: none;"></div>
<div id="lista-estacionamientos"></div>

<h2>Disponibilidad de Cajones</h2>
<form method="POST">
    <div id="contenedor-cajones" class="contenedor-cajones"></div>
</form>

<div style="text-align: center; margin-top: 40px;">
    <h3>Última Vista de Cajón</h3>
    <img id="imagen-det" src="output/frame.jpg?<?= time() ?>" alt="Detección actual" style="max-width: 600px; border-radius: 12px; border: 2px solid #bdc3c7;">
</div>

<script>
// Función para cargar cajones leyendo directamente el JSON generado por Python
function actualizarCajones() {
    fetch('output/estado_cajones.json?' + Date.now())
        .then(response => response.json())
        .then(data => {
            let contenedor = document.getElementById('contenedor-cajones');
            contenedor.innerHTML = '';
            data.forEach((estado, index) => {
                let btn = document.createElement('button');
                btn.className = 'cajon ' + (estado === 1 ? 'ocupado' : 'libre');
                btn.textContent = `Cajón ${index + 1}`;
                btn.disabled = (estado !== 0);
                if (estado === 0) {
                    btn.type = 'submit';
                    btn.name = 'cajon_id';
                    btn.value = index + 1;
                } else {
                    btn.type = 'button';
                }
                contenedor.appendChild(btn);
            });
        })
        .catch(err => console.error("Error cargando estado de cajones:", err));
}

// Actualiza imagen 
function actualizarImagen() {
    document.getElementById("imagen-det").src = "output/frame.jpg?" + Date.now();
}

// Llamar cada 2 segundos
setInterval(() => {
    actualizarCajones();
    actualizarImagen();
}, 2000);

// Primera carga
actualizarCajones();
</script>

<!-- Google Maps Script -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAQ-RUr30cGYX0a2T1u_a2Hsn20xSPD8fI&libraries=places" async defer></script>

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

</body>
</html>
