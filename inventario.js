// FUNCIONES DE INVENTARIO
function agregarProducto() {
    const producto = document.getElementById("producto").value.trim();
    const cantidad = document.getElementById("cantidad").value;
    const descripcion = document.getElementById("descripcion").value;

    mostrarError("errorInventario", "");

    if (!producto || !cantidad) {
        mostrarError("errorInventario", "El nombre y la cantidad son obligatorios.");
        return;
    }

    if (producto.length < 3) {
        mostrarError("errorInventario", "El nombre del producto debe tener mínimo 3 caracteres.");
        return;
    }

    if (cantidad <= 0) {
        mostrarError("errorInventario", "La cantidad debe ser mayor a cero.");
        return;
    }

    alert("Producto agregado correctamente");
    limpiarCampo("producto");
    limpiarCampo("cantidad");
    limpiarCampo("descripcion");
    mostrarSistema();
}
