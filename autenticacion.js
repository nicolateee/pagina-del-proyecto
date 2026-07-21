// FUNCIONES DE AUTENTICACIÓN
function mostrarError(idElemento, mensaje) {
    const elemento = document.getElementById(idElemento);
    if (elemento) {
        elemento.textContent = mensaje;
    }
}

function limpiarCampo(idElemento) {
    const elemento = document.getElementById(idElemento);
    if (elemento) {
        elemento.value = "";
    }
}

function login() {
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

    if (clave.length < 6) {
        mostrarError("errorLogin", "Contraseña debe tener al menos 6 caracteres.");
        return;
    }

    localStorage.setItem("usuarioActivo", correo);
    alert("Sesión iniciada correctamente");
    limpiarCampo("correo");
    limpiarCampo("clave");
    mostrarPrincipal();
}

function registro() {
    const correo = document.getElementById("nuevoCorreo").value.trim();
    const clave = document.getElementById("nuevaClave").value.trim();

    mostrarError("errorRegistro", "");

    if (!correo || !clave) {
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

    localStorage.setItem("usuarioActivo", correo);
    alert("Cuenta creada correctamente");
    limpiarCampo("nuevoCorreo");
    limpiarCampo("nuevaClave");
    mostrarLogin();
}

function logout() {
    localStorage.removeItem("usuarioActivo");
    alert("Sesión cerrada correctamente");
    mostrarLogin();
}
