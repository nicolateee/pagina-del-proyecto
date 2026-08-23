<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/models/Solicitud.php';

$db = Database::obtenerConexion();
$modelo = new SolicitudModel($db);
$accion = $_GET['accion'] ?? '';
$usuario = requerirSesion();

switch ($accion) {
    case 'listar':
        // El modelo ya filtra según el rol (ver SolicitudModel::listar)
        responder('exito', 'Solicitudes obtenidas', $modelo->listar($usuario));
        break;

    case 'crear':
        requerirRol(['admin', 'solicitante']);
        $tipo = trim((string) ($_POST['tipo_servicio'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
        $prioridad = (string) ($_POST['prioridad'] ?? '');

        if ($tipo === '' || $descripcion === '' || $prioridad === '') {
            responder('error', 'Debe completar todos los campos', null, 400);
        }
        if (strlen($tipo) < 3) {
            responder('error', 'El tipo de servicio debe tener al menos 3 caracteres', null, 400);
        }
        if (strlen($descripcion) < 10) {
            responder('error', 'La descripción debe ser más detallada', null, 400);
        }

        $id = $modelo->crear($_POST, (int) $usuario['id']);
        responder('exito', 'Solicitud enviada correctamente', ['id' => $id], 201);
        break;

    case 'actualizar':
        requerirRol(['admin', 'tecnico']);
        $id = (int) ($_POST['id'] ?? 0);
        $estado = (string) ($_POST['estado'] ?? '');
        if ($id <= 0) responder('error', 'ID inválido', null, 400);

        // El técnico que atiende la solicitud queda auto-asignado
        $tecnicoId = $usuario['rol'] === 'tecnico' ? (int) $usuario['id'] : null;

        if (!$modelo->actualizar($id, $estado, $tecnicoId)) {
            responder('error', 'Estado inválido', null, 400);
        }
        responder('exito', 'Solicitud actualizada correctamente', null);
        break;

    default:
        responder('error', 'Acción no reconocida', null, 400);
}
