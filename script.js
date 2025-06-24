document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("formLogin").addEventListener("submit", function(event) {
        event.preventDefault();

        const datos = new FormData();
        datos.append("nombre", document.getElementById("nombre").value);
        datos.append("matricula", document.getElementById("matricula").value);

        fetch("validar_login.php", {
            method: "POST",
            body: datos
        })
        .then(response => response.text())
        .then(data => {
            data = data.trim(); // Eliminar espacios vacíos por si acaso

            if (data === "admin") {
                window.location.href = "admin_dashboard.php";
            } else if (data === "usuario") {
                window.location.href = "principal.php";
            } else {
                // Mostrar modal de error
                const modal = document.getElementById("modalError");
                const mensajeModal = document.getElementById("modalMensaje");
                const span = document.querySelector(".close");

                mensajeModal.textContent = "Credenciales inválidas. Intenta nuevamente.";
                modal.style.display = "block";

                // Cerrar al hacer clic en la X
                span.onclick = () => modal.style.display = "none";

                // Cerrar al hacer clic fuera del modal
                window.onclick = (event) => {
                    if (event.target === modal) {
                        modal.style.display = "none";
                    }
                };
            }
        })
        .catch(error => {
            console.error("Error en la solicitud:", error);
        });
    });
});