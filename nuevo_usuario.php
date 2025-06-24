<?php
$conexion = new mysqli("localhost", "root", "", "bite");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $matricula = $_POST['matricula'];
    $rol = $_POST['rol'];

    $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, matricula, rol) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $matricula, $rol);
    $stmt->execute();

    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Usuario</title>
    <link rel="stylesheet" href="sty.css">
</head>
<body>

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

    <h1>Registrar Nuevo Usuario</h1>

    <form method="post" style="width: 90%; max-width: 500px; margin: 20px auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); animation: fadeIn 1s ease-in;">
        <label>Nombre:
            <input type="text" name="nombre" required style="width: 100%; padding: 10px; margin: 5px 0 15px; border: 1px solid #ccc; border-radius: 5px;">
        </label><br>

        <label>Matrícula:
            <input type="text" name="matricula" required style="width: 100%; padding: 10px; margin: 5px 0 15px; border: 1px solid #ccc; border-radius: 5px;">
        </label><br>

        <label>Rol:
            <select name="rol" required style="width: 100%; padding: 10px; margin: 5px 0 20px; border: 1px solid #ccc; border-radius: 5px;">
                <option value="usuario">Usuario</option>
                <option value="admin">Admin</option>
            </select>
        </label><br>

        <button type="submit" style="background-color: #3498db; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; margin-right: 10px;">Registrar</button>

        <a href="admin_dashboard.php">
            <button type="button" style="background-color: #e67e22; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer;">Cancelar</button>
        </a>
    </form>

</body>
</html>
