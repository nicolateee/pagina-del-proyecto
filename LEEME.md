# SGRSI — Puesta en marcha (XAMPP)

## 1. Ubicación del proyecto
Copiá toda esta carpeta dentro de `htdocs` de tu XAMPP, por ejemplo:
`C:\xampp\htdocs\sgrsi\`

## 2. Base de datos
1. Iniciá Apache y MySQL desde el panel de XAMPP.
2. Abrí `http://localhost/phpmyadmin`.
3. Pestaña **Importar** → seleccioná `setup.sql` → Ejecutar.
   (Esto crea la base `sgrsi_db`, las 4 tablas y un usuario admin de arranque.)

## 3. Ajustar credenciales de conexión (si hace falta)
En `api/config.php` están el host/usuario/clave de MySQL. Por defecto XAMPP usa
`root` sin contraseña, así que no debería requerir cambios.

## 4. Probar
Entrá a `http://localhost/sgrsi/pagina.html` y logueate con:

- **Correo:** `admin@sgrsi.com`
- **Clave:** `admin123`

⚠️ Cambiá esta contraseña (o borrá el usuario y creá el tuyo) antes de mostrarlo
en la entrega — quedó documentada acá para que puedas entrar la primera vez.

## 5. Roles para probar permisos
Registrate con una cuenta nueva desde `registro.html` → queda con rol `usuario`
por defecto. Con el admin logueado, andá a **Gestión de Usuarios** y cambiale
el rol a `solicitante` / `tecnico` / `admin` para probar cada vista.

## Qué se agregó respecto a lo que tenías
- Carpeta `api/` completa: `config.php` (conexión PDO + prepared statements),
  modelos en `api/models/` (Usuario, Inventario, Incidencia, Solicitud) y los
  5 endpoints (`auth`, `inventario`, `incidencias`, `solicitudes`, `usuarios`).
- Contraseñas con `password_hash()` / `password_verify()` — nunca en texto plano.
- RBAC validado **server-side** en cada endpoint (no solo ocultando botones en el JS).
- Contrato de respuesta unificado `{status, message, data}` en toda la API
  (antes el frontend esperaba `{exito, datos, error}` — ajustado en los `.js`).
- Filtrado de datos por rol en incidencias/solicitudes (cada uno ve lo que le
  corresponde, no solo se le ocultan botones).
- `escapeHtml()` en `navegacion.js`, aplicado a todos los campos de texto que
  vienen del servidor antes de insertarlos en el DOM (mitiga XSS).
- Edición de equipos en Inventario (antes solo había alta y baja).
- Media queries básicas en `layout.css` / `base.css` / `componentes.css`
  (antes no había ninguna y el layout se rompía en pantallas chicas).

## Lo que todavía NO está (para tu lista de Trello)
- Módulo de **Préstamos de equipos** (punto C de la letra) — no tocado, es
  tabla + pantalla nueva.
- **Dashboard de métricas** (Toma de Decisiones).
- **Historial/trazabilidad de equipos**.
- Campo de **diagnóstico/notas técnicas** en incidencias (hoy solo cambia estado).
- Interfaz en inglés.
- CSRF tokens.

⚠️ Como siempre: revisá el código y reescribilo con criterio propio antes de
entregarlo — esto es una base funcional, no el entregable final.
