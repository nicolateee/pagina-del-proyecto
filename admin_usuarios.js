// FUNCIONES DE ADMINISTRACIÓN DE USUARIOS
document.addEventListener('DOMContentLoaded', async () => {
    const usuario = await verificarSesion();
    if (!usuario || usuario.rol !== 'admin') {
        alert('Acceso denegado. Solo administradores.');
        window.location.href = 'principal.html';
        return;
    }
    cargarUsuarios();
});

async function cargarUsuarios() {
    try {
        const resp = await fetch('api/usuarios.php?accion=listar');
        const data = await resp.json();
        const tbody = document.getElementById('bodyUsuarios');

        if (data.status === 'exito' && data.data.length > 0) {
            tbody.innerHTML = data.data.map(u => `
                <tr>
                    <td>${u.id}</td>
                    <td>${escapeHtml(u.nombre) || '-'}</td>
                    <td>${escapeHtml(u.correo)}</td>
                    <td>
                        <select onchange="cambiarRol(${u.id}, this.value)" style="width:auto;padding:5px;font-size:12px;">
                            <option value="usuario" ${u.rol === 'usuario' ? 'selected' : ''}>Usuario</option>
                            <option value="solicitante" ${u.rol === 'solicitante' ? 'selected' : ''}>Solicitante</option>
                            <option value="tecnico" ${u.rol === 'tecnico' ? 'selected' : ''}>Técnico</option>
                            <option value="admin" ${u.rol === 'admin' ? 'selected' : ''}>Admin</option>
                        </select>
                    </td>
                    <td>${u.activo ? '<span style="color:green;">Activo</span>' : '<span style="color:red;">Inactivo</span>'}</td>
                    <td>${new Date(u.fecha_creacion).toLocaleDateString()}</td>
                    <td>
                        <button onclick="toggleActivo(${u.id}, ${u.activo ? 0 : 1})" 
                                style="background:${u.activo ? '#e74c3c' : '#27ae60'};width:auto;padding:5px 10px;font-size:12px;">
                            ${u.activo ? 'Desactivar' : 'Activar'}
                        </button>
                    </td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="7">No hay usuarios registrados</td></tr>';
        }
    } catch (err) {
        document.getElementById('bodyUsuarios').innerHTML = '<tr><td colspan="7">Error al cargar usuarios</td></tr>';
    }
}

async function cambiarRol(id, nuevoRol) {
    try {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('nuevo_rol', nuevoRol);

        const resp = await fetch('api/usuarios.php?accion=cambiar_rol', {
            method: 'POST',
            body: formData
        });
        const data = await resp.json();
        if (data.status === 'exito') {
            alert('Rol actualizado correctamente');
        } else {
            alert(data.message || 'Error al cambiar rol');
            cargarUsuarios(); // Reload to revert the select
        }
    } catch (err) {
        alert('Error de conexión');
        cargarUsuarios();
    }
}

async function toggleActivo(id, nuevoEstado) {
    try {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('activo', nuevoEstado);

        const resp = await fetch('api/usuarios.php?accion=activar_desactivar', {
            method: 'POST',
            body: formData
        });
        const data = await resp.json();
        if (data.status === 'exito') {
            cargarUsuarios();
        } else {
            alert(data.message || 'Error al cambiar estado');
        }
    } catch (err) {
        alert('Error de conexión');
    }
}
