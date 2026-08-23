<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/models/Usuario.php';

$db = Database::obtenerConexion();
$modelo = new Usuario($db);
$accion = $_GET['accion'] ?? '';

requerirRol(['admin']); // todo este endpoint es exclusivo de admin

switch ($accion) {
    case 'listar':
        responder('exito', 'Usuarios obtenidos', $modelo->listarTodos());
        break;

    case 'cambiar_rol':
        $id = (int) ($_POST['id'] ?? 0);
        $nuevoRol = (string) ($_POST['nuevo_rol'] ?? '');
        if ($id <= 0) responder('error', 'ID inválido', null, 400);

        if (!$modelo->cambiarRol($id, $nuevoRol)) {
            responder('error', 'Rol inválido', null, 400);
        }
        responder('exito', 'Rol actualizado correctamente', null);
        break;

    case 'activar_desactivar':
        $id = (int) ($_POST['id'] ?? 0);
        $activo = (bool) ($_POST['activo'] ?? false);
        if ($id <= 0) responder('error', 'ID inválido', null, 400);

        $modelo->activarDesactivar($id, $activo);
        responder('exito', 'Estado actualizado correctamente', null);
        break;

    default:
        responder('error', 'Acción no reconocida', null, 400);
}
