<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/models/Usuario.php';

$db = Database::obtenerConexion();
$usuarioModel = new Usuario($db);
$accion = $_GET['accion'] ?? '';

switch ($accion) {
    case 'login':
        $correo = trim((string) ($_POST['correo'] ?? ''));
        $clave  = (string) ($_POST['clave'] ?? '');

        if ($correo === '' || $clave === '') {
            responder('error', 'Correo y contraseña son obligatorios', null, 400);
        }

        $u = $usuarioModel->verificarCredenciales($correo, $clave);
        if (!$u) {
            responder('error', 'Credenciales inválidas o usuario inactivo', null, 401);
        }

        $_SESSION['usuario'] = [
            'id'     => (int) $u['id'],
            'nombre' => $u['nombre'],
            'correo' => $u['correo'],
            'rol'    => $u['rol'],
        ];
        responder('exito', 'Sesión iniciada', $_SESSION['usuario']);
        break;

    case 'registro':
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $correo = trim((string) ($_POST['correo'] ?? ''));
        $clave  = (string) ($_POST['clave'] ?? '');

        if ($nombre === '' || $correo === '' || $clave === '') {
            responder('error', 'Todos los campos son obligatorios', null, 400);
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            responder('error', 'Correo inválido', null, 400);
        }
        if (strlen($clave) < 8) {
            responder('error', 'La contraseña debe tener al menos 8 caracteres', null, 400);
        }
        if ($usuarioModel->buscarPorCorreo($correo)) {
            responder('error', 'Ya existe una cuenta con ese correo', null, 409);
        }

        // Alta de cuenta: rol "usuario" por defecto. Solo el admin puede promoverlo.
        $usuarioModel->crear($nombre, $correo, $clave, 'usuario');
        responder('exito', 'Cuenta creada correctamente', null, 201);
        break;

    case 'logout':
        $_SESSION = [];
        session_destroy();
        responder('exito', 'Sesión cerrada', null);
        break;

    case 'sesion':
        $u = usuarioActual();
        if ($u) {
            responder('exito', 'Sesión activa', $u);
        }
        responder('error', 'No hay sesión activa', null, 401);
        break;

    default:
        responder('error', 'Acción no reconocida', null, 400);
}
