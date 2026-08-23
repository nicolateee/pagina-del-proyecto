// FUNCIONES DE NAVEGACIÓN
function irAPagina(ruta) {
    window.location.href = ruta;
}

function mostrarLogin() { irAPagina("pagina.html"); }
function mostrarRegistro() { irAPagina("registro.html"); }
function mostrarSistema() { irAPagina("sistema.html"); }
function mostrarInventario() { irAPagina("inventario.html"); }
function mostrarIncidencias() { irAPagina("incidencias.html"); }
function mostrarServicio() { irAPagina("servicio.html"); }
function mostrarPrincipal() { irAPagina("principal.html"); }
function mostrarAdminUsuarios() { irAPagina("admin_usuarios.html"); }

// Utilidad compartida: escapa texto antes de insertarlo con innerHTML,
// para evitar inyección de HTML/JS desde datos que vienen del servidor.
function escapeHtml(texto) {
    if (texto === null || texto === undefined) return '';
    return String(texto)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}
