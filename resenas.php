<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reseñas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Reseñas de usuarios</h1>
    <a href="principal.php">← Volver a la página principal</a>

    <hr>
    <h2>Deja tu reseña</h2>
    <form method="POST" action="guardar_resena.php" id="resena-form">
        <label for="comentario">Comentario:</label><br>
        <textarea name="comentario" rows="4" cols="50" required placeholder="Escribe tu experiencia..."></textarea><br><br>
        
        <label>Calificación:</label>
        <div class="star-rating">
            <span data-value="5">⭐</span>
            <span data-value="4">⭐</span>
            <span data-value="3">⭐</span>
            <span data-value="2">⭐</span>
            <span data-value="1">⭐</span>
        </div>
        <input type="hidden" name="calificacion" id="calificacion" value="5">
        <button type="submit">Enviar reseña</button>
    </form>

    <h2>Reseñas recientes</h2>
    <div id="resenas">
        <?php
        $conn = new mysqli("localhost", "root", "", "bite");
        if ($conn->connect_error) die("Error de conexión: " . $conn->connect_error);

        $sql = "SELECT usuario, comentario, calificacion, fecha FROM reseñas ORDER BY fecha DESC LIMIT 10";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            echo "<div class='resena'>";
            echo "<div class='resena-header'><strong>" . htmlspecialchars($row['usuario']) . "</strong>";
            echo "<span class='estrellas'>" . str_repeat("⭐", $row['calificacion']) . "</span></div>";
            echo "<small>" . $row['fecha'] . "</small>";
            echo "<p>" . nl2br(htmlspecialchars($row['comentario'])) . "</p>";
            echo "</div>";
        }

        $conn->close();
        ?>
    </div>

    <script>
        // Script para manejar estrellas
        document.querySelectorAll('.star-rating span').forEach(star => {
            star.addEventListener('click', () => {
                const rating = star.getAttribute('data-value');
                document.getElementById('calificacion').value = rating;
                document.querySelectorAll('.star-rating span').forEach(s => {
                    s.style.color = s.getAttribute('data-value') <= rating ? 'gold' : 'gray';
                });
            });
        });
    </script>
</body>
</html>
