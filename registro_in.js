document.getElementById("formulario").addEventListener("submit", function(event) {
    event.preventDefault();

    const nombre = document.getElementById("nombre").value.trim();
    const matricula = document.getElementById("matricula").value.trim();

    const soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;
    const formatoMatricula = /^[A-Z0-9\-]+$/;

    if (!soloLetras.test(nombre)) {
        mostrarModal("El nombre solo debe contener letras y espacios.");
        return;
    }

    if (!formatoMatricula.test(matricula)) {
        mostrarModal("La matrícula solo debe contener letras mayúsculas, números y guiones.");
        return;
    }

    const datos = new FormData();
    datos.append("nombre", nombre);
    datos.append("matricula", matricula);

    fetch("guardar_datos.php", {
        method: "POST",
        body: datos
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById("respuesta").innerHTML = data;
        document.getElementById("formulario").reset();
    });
});

function mostrarModal(mensaje) {
    const modal = document.getElementById("modalError");
    const modalMensaje = document.getElementById("modalMensaje");
    const span = modal.querySelector(".close");

    modalMensaje.textContent = mensaje;
    modal.style.display = "block";

    span.onclick = () => modal.style.display = "none";
    window.onclick = (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };
}
