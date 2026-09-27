// FUNCIONES DE AUTENTICACIÓN

// Helper para mostrar errores
function mostrarError(idElemento, mensaje) {
    const elemento = document.getElementById(idElemento);
    if (elemento) elemento.textContent = mensaje;
}

function limpiarCampo(idElemento) {
    const elemento = document.getElementById(idElemento);
    if (elemento) elemento.value = "";
}

// Login con fetch
async function login() {
    const correo = document.getElementById("correo").value.trim();
    const clave = document.getElementById("clave").value.trim();
    mostrarError("errorLogin", "");

    if (!correo || !clave) {
        mostrarError("errorLogin", "Por favor ingresa correo y contraseña.");
        return;
    }
    if (!correo.includes("@")) {
        mostrarError("errorLogin", "Correo inválido.");
        return;
    }

    try {
    const formData = new FormData();
    formData.append('correo', correo);
    formData.append('clave', clave);

    const resp = await fetch('api/auth.php?accion=login', {
        method: 'POST',
        body: formData
    });

    // Ver qué está devolviendo realmente PHP
    const texto = await resp.text();

    console.log("HTTP status:", resp.status);
    console.log("Respuesta del servidor:", texto);

    let data;

    try {
        data = JSON.parse(texto);
    } catch (e) {
        console.error("La respuesta NO es JSON válido:", texto);
        mostrarError("errorLogin", "El servidor no devolvió un JSON válido.");
        return;
    }

    if (data.status === 'exito') {
        sessionStorage.setItem('usuario', JSON.stringify(data.data));
        window.location.href = 'principal.html';
    } else {
        mostrarError(
            "errorLogin",
            data.message || "Error al iniciar sesión"
        );
    }

} catch (err) {
    console.error("Error en fetch:", err);
    mostrarError("errorLogin", "Error de conexión con el servidor");
}

}

// Registro con fetch  
async function registro() {
    const nombre = document.getElementById("nuevoNombre").value.trim();
    const correo = document.getElementById("nuevoCorreo").value.trim();
    const clave = document.getElementById("nuevaClave").value.trim();
    mostrarError("errorRegistro", "");

    if (!nombre || !correo || !clave) {
        mostrarError("errorRegistro", "Por favor completa todos los campos.");
        return;
    }
    if (!correo.includes("@")) {
        mostrarError("errorRegistro", "Correo inválido.");
        return;
    }
    if (clave.length < 8) {
        mostrarError("errorRegistro", "Contraseña debe tener al menos 8 caracteres.");
        return;
    }

    try {
        const formData = new FormData();
        formData.append('nombre', nombre);
        formData.append('correo', correo);
        formData.append('clave', clave);

        const resp = await fetch('api/auth.php?accion=registro', {
            method: 'POST',
            body: formData
        });
        const data = await resp.json();

        if (data.status === 'exito') {
            alert("Cuenta creada correctamente. Ahora podés iniciar sesión.");
            window.location.href = 'pagina.html';
        } else {
            mostrarError("errorRegistro", data.message || "Error al crear cuenta");
        }
    } catch (err) {
        mostrarError("errorRegistro", "Error de conexión con el servidor");
    }
}

// Logout
async function logout() {
    try {
        await fetch('api/auth.php?accion=logout', { method: 'POST' });
    } catch (e) {}
    sessionStorage.removeItem('usuario');
    window.location.href = 'pagina.html';
}

// Verificar sesión activa - call on protected pages
async function verificarSesion() {
    try {
        const resp = await fetch('api/auth.php?accion=sesion');
        const data = await resp.json();
        if (data.status === 'exito' && data.data) {
            sessionStorage.setItem('usuario', JSON.stringify(data.data));
            return data.data;
        }
    } catch (e) {}
    // No session, redirect to login
    window.location.href = 'pagina.html';
    return null;
}

// Get current user from sessionStorage (fast, no network)
function getUsuarioActual() {
    const u = sessionStorage.getItem('usuario');
    return u ? JSON.parse(u) : null;
}

// Check if user has one of the allowed roles
function tieneRol(rolesPermitidos) {
    const u = getUsuarioActual();
    return u && rolesPermitidos.includes(u.rol);
}
