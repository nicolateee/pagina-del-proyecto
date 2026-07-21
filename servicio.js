// FUNCIONES DE SERVICIO
function enviarSolicitud() {
    const tipo = document.getElementById("tipoServicio").value.trim();
    const descripcion = document.getElementById("descripcionServicio").value.trim();
    const prioridad = document.getElementById("prioridad").value.trim().toLowerCase();

    mostrarError("errorServicio", "");

    if (!tipo || !descripcion || !prioridad) {
        mostrarError("errorServicio", "Debe completar todos los campos.");
        return;
    }

    if (tipo.length < 3) {
        mostrarError("errorServicio", "El tipo de servicio debe tener al menos 3 caracteres.");
        return;
    }

    if (descripcion.length < 10) {
        mostrarError("errorServicio", "La descripción debe ser más detallada.");
        return;
    }

    if (prioridad !== "alta" && prioridad !== "media" && prioridad !== "baja") {
        mostrarError("errorServicio", "La prioridad debe ser Alta, Media o Baja.");
        return;
    }

    alert("Solicitud enviada correctamente");
    limpiarCampo("tipoServicio");
    limpiarCampo("descripcionServicio");
    limpiarCampo("prioridad");
    mostrarSistema();
}
