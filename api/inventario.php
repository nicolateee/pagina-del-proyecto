<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/models/Inventario.php';

$db = Database::obtenerConexion();
$modelo = new InventarioModel($db);
$accion = $_GET['accion'] ?? '';

requerirSesion(); // cualquier rol autenticado puede consultar el inventario

switch ($accion) {
    case 'listar':
        responder('exito', 'Inventario obtenido', $modelo->listar());
        break;

    case 'agregar':
        requerirRol(['admin']);
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        if ($nombre === '') {
            responder('error', 'El nombre del equipo es obligatorio', null, 400);
        }
        $id = $modelo->agregar($_POST);
        responder('exito', 'Equipo agregado correctamente', ['id' => $id], 201);
        break;

    case 'editar':
        requerirRol(['admin']);
        $id = (int) ($_POST['id'] ?? 0);
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        if ($id <= 0) responder('error', 'ID inválido', null, 400);
        if ($nombre === '') responder('error', 'El nombre del equipo es obligatorio', null, 400);
        $modelo->editar($id, $_POST);
        responder('exito', 'Equipo actualizado correctamente', null);
        break;

    case 'eliminar':
        requerirRol(['admin']);
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) responder('error', 'ID inválido', null, 400);
        $modelo->eliminar($id);
        responder('exito', 'Equipo eliminado correctamente', null);
        break;

    default:
        responder('error', 'Acción no reconocida', null, 400);
}
