<?php
// editar_usuario.php
$conexion = new mysqli("localhost", "root", "", "bite");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $matricula = $_POST['matricula'];
    $rol = $_POST['rol'];

    $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, matricula = ?, rol = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nombre, $matricula, $rol, $id);
    $stmt->execute();

    header("Location: admin_dashboard.php");
    exit();
}

$id = $_GET['id'];
$result = $conexion->query("SELECT * FROM usuarios WHERE id = $id");
$usuario = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
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

    <h1>Editar Usuario</h1>

    <form method="post" style="width: 90%; max-width: 500px; margin: 20px auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); animation: fadeIn 1s ease-in;">
        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

        <label>Nombre:
            <input type="text" name="nombre" value="<?php echo $usuario['nombre']; ?>" required style="width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px;">
        </label><br>

        <label>Matrícula:
            <input type="text" name="matricula" value="<?php echo $usuario['matricula']; ?>" required style="width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px;">
        </label><br>

        <label>Rol:
            <select name="rol" required style="width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px;">
                <option value="admin" <?php if ($usuario['rol'] === 'admin') echo 'selected'; ?>>Admin</option>
                <option value="usuario" <?php if ($usuario['rol'] === 'usuario') echo 'selected'; ?>>Usuario</option>
            </select>
        </label><br>

        <button type="submit" style="background-color: #2ecc71; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; margin-right: 10px;">Guardar cambios</button>

        <a href="admin_dashboard.php">
            <button type="button" style="background-color: #e74c3c; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer;">Cancelar</button>
        </a>
    </form>

</body>
</html>