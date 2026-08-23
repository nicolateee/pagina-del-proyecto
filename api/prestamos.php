<?php
require_once 'config.php';
$usr = verificarSesion();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === 'GET') {
    // Listar prestamos
    if (in_array($usr['rol'], ['admin', 'tecnico'])) {
        $sql = "SELECT p.*, u.nombre as nombre_usuario, u.apellido as apellido_usuario, e.modulo as equipo_modulo, e.nro_serie 
                FROM Prestamo p 
                JOIN Usuario u ON p.id_usuario = u.id_usuario 
                JOIN Equipo e ON p.id_equipo = e.id_equipo 
                ORDER BY p.f_prestamo DESC";
        $stmt = $pdo->query($sql);
    } else {
        $sql = "SELECT p.*, u.nombre as nombre_usuario, u.apellido as apellido_usuario, e.modulo as equipo_modulo, e.nro_serie 
                FROM Prestamo p 
                JOIN Usuario u ON p.id_usuario = u.id_usuario 
                JOIN Equipo e ON p.id_equipo = e.id_equipo 
                WHERE p.id_usuario = ? 
                ORDER BY p.f_prestamo DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usr['id_usuario']]);
    }
    responder("success", "Lista de prestamos", $stmt->fetchAll());
}
elseif ($method === 'POST') {
    // Crear prestamo (Usuario solicita)
    $id_equipo = $_POST['id_equipo'] ?? null;
    $motivo = $_POST['motivo'] ?? '';
    
    if (!$id_equipo || empty($motivo)) responder("error", "Equipo y motivo son obligatorios.", null, 400);

    // Verificar si el equipo está disponible
    $check = $pdo->prepare("SELECT estado FROM Equipo WHERE id_equipo = ?");
    $check->execute([$id_equipo]);
    $equipo = $check->fetch();
    
    if (!$equipo) responder("error", "El equipo no existe.", null, 404);
    if ($equipo['estado'] !== 'Activo') responder("error", "El equipo no está disponible para préstamo.", null, 400);
    
    // Verificar que no haya un préstamo activo para ese equipo
    $checkPrestamo = $pdo->prepare("SELECT id_prestamo FROM Prestamo WHERE id_equipo = ? AND estado IN ('Solicitado', 'Aprobado', 'Entregado')");
    $checkPrestamo->execute([$id_equipo]);
    if ($checkPrestamo->rowCount() > 0) responder("error", "El equipo ya está prestado o en proceso.", null, 400);

    $sql = "INSERT INTO Prestamo (id_usuario, id_equipo, motivo, f_prestamo, estado) VALUES (?, ?, ?, NOW(), 'Solicitado')";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$usr['id_usuario'], $id_equipo, $motivo])) {
        
        $id_prestamo = $pdo->lastInsertId();
        
        // Historial
        $historial = $pdo->prepare("INSERT INTO Historial (id_equipo, id_usuario, fecha_evento, tipo_evento, descripcion_evento) VALUES (?, ?, NOW(), 'PRESTAMO_SOLICITADO', 'Préstamo solicitado por ' . ?)");
        $historial->execute([$id_equipo, $usr['id_usuario'], $usr['nombre']]);

        responder("success", "Préstamo solicitado correctamente.", ["id" => $id_prestamo], 201);
    }
    responder("error", "Error al solicitar préstamo.", null, 500);
}
elseif ($method === 'PUT') {
    verificarRol(['admin', 'tecnico']);
    parse_str(file_get_contents("php://input"), $_PUT);
    $id_prestamo = $_PUT['id_prestamo'] ?? null;
    $estado = $_PUT['estado'] ?? null;
    
    if (!$id_prestamo || !$estado) responder("error", "Faltan datos.", null, 400);

    $sql = "UPDATE Prestamo SET estado = ?";
    if ($estado === 'Devuelto' || $estado === 'Rechazado') {
        $sql .= ", f_devolucion = NOW() ";
    }
    $sql .= " WHERE id_prestamo = ?";
    
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$estado, $id_prestamo])) {
        
        // Registrar en historial del equipo
        $prestamoInfo = $pdo->prepare("SELECT id_equipo, id_usuario FROM Prestamo WHERE id_prestamo = ?");
        $prestamoInfo->execute([$id_prestamo]);
        $info = $prestamoInfo->fetch();
        
        if ($info) {
            $desc = "Estado de préstamo cambiado a " . $estado;
            $hist = $pdo->prepare("INSERT INTO Historial (id_equipo, id_usuario, fecha_evento, tipo_evento, descripcion_evento) VALUES (?, ?, NOW(), 'PRESTAMO_ACTUALIZADO', ?)");
            $hist->execute([$info['id_equipo'], $usr['id_usuario'], $desc]);
            
            // Si fue devuelto, asegurar que el equipo quede Activo de nuevo
            if ($estado === 'Devuelto') {
                $pdo->prepare("UPDATE Equipo SET estado = 'Activo' WHERE id_equipo = ?")->execute([$info['id_equipo']]);
            }
        }
        
        responder("success", "Préstamo actualizado.");
    }
    responder("error", "Error al actualizar.", null, 500);
}

responder("error", "Método no permitido.", null, 405);
?>
