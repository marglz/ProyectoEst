<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.html");
    exit;
}

$conexion = new mysqli("localhost", "root", "", "bite");
$resultado = $conexion->query("SELECT id, nombre, matricula, rol FROM usuarios");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="sty.css">
</head>
<body class="admin-body">
    
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

    <h2>Vista en Vivo del Estacionamiento</h2>
<div style="text-align:center;">
    <img id="video-frame" src="output/frame.jpg" style="max-width: 100%; border: 2px solid black;">
</div>

<script>
    // Refrescar imagen cada segundo (2000 ms)
    setInterval(() => {
        const img = document.getElementById("video-frame");
        img.src = "output/frame.jpg?t=" + new Date().getTime(); // evita caché
    }, 700);
</script>


    <div class="admin-container">
        <h1>Panel de administración de usuarios</h1>
        <a href="nuevo_usuario.php" class="btn-agregar">➕ Agregar nuevo usuario</a>
        <table class="tabla-usuarios">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Matrícula</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $fila['id']; ?></td>
                    <td><?php echo $fila['nombre']; ?></td>
                    <td><?php echo $fila['matricula']; ?></td>
                    <td><?php echo $fila['rol']; ?></td>
                    <td>
                        <a href="editar_usuario.php?id=<?php echo $fila['id']; ?>" class="btn-editar">✏️ Editar</a>
                        <a href="eliminar_usuario.php?id=<?php echo $fila['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Seguro que quieres eliminar este usuario?');">🗑️ Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
