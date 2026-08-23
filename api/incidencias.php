<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/models/Incidencia.php';

$db = Database::obtenerConexion();
$modelo = new IncidenciaModel($db);
$accion = $_GET['accion'] ?? '';
$usuario = requerirSesion();

switch ($accion) {
    case 'listar':
        // El modelo ya filtra según el rol (ver IncidenciaModel::listar)
        responder('exito', 'Incidencias obtenidas', $modelo->listar($usuario));
        break;

    case 'crear':
        // Los 4 roles pueden registrar incidencias
        $titulo = trim((string) ($_POST['titulo'] ?? ''));
        $descripcion = trim((string) ($_POST['descripcion'] ?? ''));
        $area = trim((string) ($_POST['area'] ?? ''));

        if ($titulo === '' || $descripcion === '' || $area === '') {
            responder('error', 'Debe completar todos los campos', null, 400);
        }
        if (strlen($titulo) < 5) {
            responder('error', 'El título debe tener al menos 5 caracteres', null, 400);
        }
        if (strlen($descripcion) < 10) {
            responder('error', 'La descripción debe ser más detallada', null, 400);
        }

        $id = $modelo->crear($_POST, (int) $usuario['id']);
        responder('exito', 'Incidencia guardada correctamente', ['id' => $id], 201);
        break;

    case 'actualizar':
        requerirRol(['admin', 'tecnico']);
        $id = (int) ($_POST['id'] ?? 0);
        $estado = (string) ($_POST['estado'] ?? '');
        if ($id <= 0) responder('error', 'ID inválido', null, 400);

        // El técnico que atiende la incidencia queda auto-asignado
        $asignadoA = $usuario['rol'] === 'tecnico' ? (int) $usuario['id'] : null;

        if (!$modelo->actualizar($id, $estado, $asignadoA)) {
            responder('error', 'Estado inválido', null, 400);
        }
        responder('exito', 'Incidencia actualizada correctamente', null);
        break;

    default:
        responder('error', 'Acción no reconocida', null, 400);
}
