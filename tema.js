// FUNCIONES DE TEMA Y CONFIGURACIÓN
function toggleDark() {
    document.body.classList.toggle("dark");

    if (document.body.classList.contains("dark")) {
        localStorage.setItem("modo", "dark");
    } else {
        localStorage.setItem("modo", "light");
    }
}

window.addEventListener("DOMContentLoaded", function () {
    if (localStorage.getItem("modo") === "dark") {
        document.body.classList.add("dark");
    }
});
