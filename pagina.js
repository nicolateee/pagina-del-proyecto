        // FUNCIONES DE NAVEGACIÓN
        function mostrarLogin() {
            ocultarTodo();
            document.getElementById("loginSection").classList.remove("hidden");
        }

        function mostrarRegistro() {
            ocultarTodo();
            document.getElementById("registroSection").classList.remove("hidden");
        }

        function mostrarSistema() {
            ocultarTodo();
            document.getElementById("sistemaSection").classList.remove("hidden");
        }

        function mostrarInventario() {
            ocultarTodo();
            document.getElementById("inventarioSection").classList.remove("hidden");
        }

        function mostrarIncidencias() {
            ocultarTodo();
            document.getElementById("incidenciasSection").classList.remove("hidden");
        }

        function mostrarServicio() {
            ocultarTodo();
            document.getElementById("servicioSection").classList.remove("hidden");
        }

        function mostrarPrincipal() {
            ocultarTodo();
            document.getElementById("principalSection").classList.remove("hidden");
        }

        function ocultarTodo() {
            document.getElementById("loginSection").classList.add("hidden");
            document.getElementById("registroSection").classList.add("hidden");
            document.getElementById("sistemaSection").classList.add("hidden");
            document.getElementById("inventarioSection").classList.add("hidden");
            document.getElementById("incidenciasSection").classList.add("hidden");
            document.getElementById("servicioSection").classList.add("hidden");
            document.getElementById("principalSection").classList.add("hidden");
        }

        // LOGIN
        function login() {
            const correo = document.getElementById("correo").value.trim();
            const clave = document.getElementById("clave").value.trim();
            const error = document.getElementById("errorLogin");

            error.textContent = "";

            if (!correo || !clave) {
                error.textContent = "Por favor ingresa correo y contraseña.";
                return;
            }

            if (!correo.includes("@")) {
                error.textContent = "Correo inválido.";
                return;
            }

            if (clave.length < 6) {
                error.textContent = "Contraseña debe tener al menos 6 caracteres.";
                return;
            }

            localStorage.setItem("usuarioActivo", correo);
            alert("Sesión iniciada correctamente");
            document.getElementById("correo").value = "";
            document.getElementById("clave").value = "";
            mostrarPrincipal();
        }

        // REGISTRO
        function registro() {
            const correo = document.getElementById("nuevoCorreo").value.trim();
            const clave = document.getElementById("nuevaClave").value.trim();
            const error = document.getElementById("errorRegistro");

            error.textContent = "";

            if (!correo || !clave) {
                error.textContent = "Por favor completa todos los campos.";
                return;
            }

            if (!correo.includes("@")) {
                error.textContent = "Correo inválido.";
                return;
            }

            if (clave.length < 8) {
                error.textContent = "Contraseña debe tener al menos 8 caracteres.";
                return;
            }

            localStorage.setItem("usuarioActivo", correo);
            alert("Cuenta creada correctamente");
            document.getElementById("nuevoCorreo").value = "";
            document.getElementById("nuevaClave").value = "";
            mostrarLogin();
        }

        // INVENTARIO
        function agregarProducto() {
            const producto = document.getElementById("producto").value.trim();
            const cantidad = document.getElementById("cantidad").value;
            const descripcion = document.getElementById("descripcion").value;
            const error = document.getElementById("errorInventario");

            error.textContent = "";

            if (!producto || !cantidad) {
                error.textContent = "El nombre y la cantidad son obligatorios.";
                return;
            }

            if (producto.length < 3) {
                error.textContent = "El nombre del producto debe tener mínimo 3 caracteres.";
                return;
            }

            if (cantidad <= 0) {
                error.textContent = "La cantidad debe ser mayor a cero.";
                return;
            }

            alert("Producto agregado correctamente");
            document.getElementById("producto").value = "";
            document.getElementById("cantidad").value = "";
            document.getElementById("descripcion").value = "";
        }

        // INCIDENCIAS
        function guardarIncidencia() {
            const titulo = document.getElementById("titulo").value.trim();
            const descripcion = document.getElementById("descripcionIncidencia").value.trim();
            const area = document.getElementById("area").value.trim();
            const error = document.getElementById("errorIncidencia");

            error.textContent = "";

            if (!titulo || !descripcion || !area) {
                error.textContent = "Debe completar todos los campos.";
                return;
            }

            if (titulo.length < 5) {
                error.textContent = "El título debe tener al menos 5 caracteres.";
                return;
            }

            if (descripcion.length < 10) {
                error.textContent = "La descripción debe ser más detallada.";
                return;
            }

            alert("Incidencia guardada correctamente");
            document.getElementById("titulo").value = "";
            document.getElementById("descripcionIncidencia").value = "";
            document.getElementById("area").value = "";
        }

        // SERVICIO
        function enviarSolicitud() {
            const tipo = document.getElementById("tipoServicio").value.trim();
            const descripcion = document.getElementById("descripcionServicio").value.trim();
            const prioridad = document.getElementById("prioridad").value.trim().toLowerCase();
            const error = document.getElementById("errorServicio");

            error.textContent = "";

            if (!tipo || !descripcion || !prioridad) {
                error.textContent = "Debe completar todos los campos.";
                return;
            }

            if (tipo.length < 3) {
                error.textContent = "El tipo de servicio debe tener al menos 3 caracteres.";
                return;
            }

            if (descripcion.length < 10) {
                error.textContent = "La descripción debe ser más detallada.";
                return;
            }

            if (prioridad !== "alta" && prioridad !== "media" && prioridad !== "baja") {
                error.textContent = "La prioridad debe ser Alta, Media o Baja.";
                return;
            }

            alert("Solicitud enviada correctamente");
            document.getElementById("tipoServicio").value = "";
            document.getElementById("descripcionServicio").value = "";
            document.getElementById("prioridad").value = "";
        }

        // LOGOUT
        function logout() {
            localStorage.removeItem("usuarioActivo");
            alert("Sesión cerrada correctamente");
            mostrarLogin();
        }

        // MODO OSCURO
        function toggleDark() {
            document.body.classList.toggle("dark");

            if (document.body.classList.contains("dark")) {
                localStorage.setItem("modo", "dark");
            } else {
                localStorage.setItem("modo", "light");
            }
        }

        // CARGAR CONFIGURACIÓN AL INICIAR
        window.onload = function () {
            if (localStorage.getItem("modo") === "dark") {
                document.body.classList.add("dark");
            }

            mostrarPrincipal();
        };