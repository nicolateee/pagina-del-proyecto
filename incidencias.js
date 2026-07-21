// FUNCIONES DE INCIDENCIAS
function guardarIncidencia() {
    const titulo = document.getElementById("titulo").value.trim();
    const descripcion = document.getElementById("descripcionIncidencia").value.trim();
    const area = document.getElementById("area").value.trim();

    mostrarError("errorIncidencia", "");

    if (!titulo || !descripcion || !area) {
        mostrarError("errorIncidencia", "Debe completar todos los campos.");
        return;
    }

    if (titulo.length < 5) {
        mostrarError("errorIncidencia", "El título debe tener al menos 5 caracteres.");
        return;
    }

    if (descripcion.length < 10) {
        mostrarError("errorIncidencia", "La descripción debe ser más detallada.");
        return;
    }

    alert("Incidencia guardada correctamente");
    limpiarCampo("titulo");
    limpiarCampo("descripcionIncidencia");
    limpiarCampo("area");
    mostrarSistema();
}
