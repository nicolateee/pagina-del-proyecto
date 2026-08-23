// FUNCIONES DE SERVICIO
let rolUsuarioSol = '';

document.addEventListener('DOMContentLoaded', async () => {
    const usuario = await verificarSesion();
    if (!usuario) return;
    rolUsuarioSol = usuario.rol;

    // Show form for admin and solicitante
    if (['admin', 'solicitante'].includes(rolUsuarioSol)) {
        document.getElementById('formSolicitud').style.display = 'flex';
    }

    // Admin and tecnico can manage
    if (['admin', 'tecnico'].includes(rolUsuarioSol)) {
        document.getElementById('colAccionesSol').style.display = '';
    }

    cargarSolicitudes();
});

async function cargarSolicitudes() {
    try {
        const resp = await fetch('api/solicitudes.php?accion=listar');
        const data = await resp.json();
        const tbody = document.getElementById('bodySolicitudes');

        if (data.status === 'exito' && data.data.length > 0) {
            tbody.innerHTML = data.data.map(sol => `
                <tr>
                    <td>${sol.id}</td>
                    <td>${escapeHtml(sol.tipo_servicio)}</td>
                    <td>${escapeHtml(sol.descripcion.substring(0, 50))}${sol.descripcion.length > 50 ? '...' : ''}</td>
                    <td><span class="badge badge-${sol.prioridad}">${sol.prioridad.charAt(0).toUpperCase() + sol.prioridad.slice(1)}</span></td>
                    <td><span class="badge badge-${sol.estado}">${formatEstadoSol(sol.estado)}</span></td>
                    <td>${escapeHtml(sol.nombre_solicitante) || '-'}</td>
                    <td>${escapeHtml(sol.nombre_tecnico) || 'Sin asignar'}</td>
                    <td>${new Date(sol.fecha_creacion).toLocaleDateString()}</td>
                    ${['admin', 'tecnico'].includes(rolUsuarioSol) ? `<td>
                        <select onchange="actualizarSolicitud(${sol.id}, this.value)" style="width:auto;padding:5px;font-size:12px;">
                            <option value="" disabled selected>Cambiar estado</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="en_proceso">En proceso</option>
                            <option value="completada">Completada</option>
                            <option value="rechazada">Rechazada</option>
                        </select>
                    </td>` : ''}
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="9">No hay solicitudes registradas</td></tr>';
        }
    } catch (err) {
        document.getElementById('bodySolicitudes').innerHTML = '<tr><td colspan="9">Error al cargar solicitudes</td></tr>';
    }
}

function formatEstadoSol(estado) {
    const map = { 'pendiente': 'Pendiente', 'en_proceso': 'En proceso', 'completada': 'Completada', 'rechazada': 'Rechazada' };
    return map[estado] || estado;
}

async function enviarSolicitud() {
    const tipo = document.getElementById('tipoServicio').value.trim();
    const descripcion = document.getElementById('descripcionServicio').value.trim();
    const prioridad = document.getElementById('prioridad').value;

    mostrarError('errorServicio', '');

    if (!tipo || !descripcion || !prioridad) {
        mostrarError('errorServicio', 'Debe completar todos los campos.');
        return;
    }
    if (tipo.length < 3) {
        mostrarError('errorServicio', 'El tipo de servicio debe tener al menos 3 caracteres.');
        return;
    }
    if (descripcion.length < 10) {
        mostrarError('errorServicio', 'La descripción debe ser más detallada.');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('tipo_servicio', tipo);
        formData.append('descripcion', descripcion);
        formData.append('prioridad', prioridad);

        const resp = await fetch('api/solicitudes.php?accion=crear', {
            method: 'POST',
            body: formData
        });
        const data = await resp.json();

        if (data.status === 'exito') {
            alert('Solicitud enviada correctamente');
            limpiarCampo('tipoServicio');
            limpiarCampo('descripcionServicio');
            document.getElementById('prioridad').selectedIndex = 0;
            cargarSolicitudes();
        } else {
            mostrarError('errorServicio', data.message || 'Error al enviar');
        }
    } catch (err) {
        mostrarError('errorServicio', 'Error de conexión');
    }
}

async function actualizarSolicitud(id, nuevoEstado) {
    try {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('estado', nuevoEstado);

        const resp = await fetch('api/solicitudes.php?accion=actualizar', {
            method: 'POST',
            body: formData
        });
        const data = await resp.json();
        if (data.status === 'exito') {
            cargarSolicitudes();
        } else {
            alert(data.message || 'Error al actualizar');
        }
    } catch (err) {
        alert('Error de conexión');
    }
}
