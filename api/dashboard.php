<?php
require_once 'config.php';
$usr = verificarSesion();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Solo admin y tecnicos ven el dashboard completo, solicitantes ven sus propias estadisticas.
    $isAdmin = in_array($usr['rol'], ['admin', 'tecnico']);
    $id = $usr['id_usuario'];
    
    $stats = [];
    
    // Equipos totales
    $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN estado = 'Activo' THEN 1 ELSE 0 END) as activos FROM Equipo");
    $stats['equipos'] = $stmt->fetch();
    
    // Incidencias
    if ($isAdmin) {
        $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN estado != 'Resuelta' AND estado != 'Cerrada' THEN 1 ELSE 0 END) as abiertas FROM Incidencia");
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN estado != 'Resuelta' AND estado != 'Cerrada' THEN 1 ELSE 0 END) as abiertas FROM Incidencia WHERE id_usuario = ?");
        $stmt->execute([$id]);
    }
    $stats['incidencias'] = $stmt->fetch();
    
    // Solicitudes
    if ($isAdmin) {
        $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes FROM Solicitud");
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes FROM Solicitud WHERE id_solicitante = ?");
        $stmt->execute([$id]);
    }
    $stats['solicitudes'] = $stmt->fetch();
    
    // Préstamos Activos (No devueltos/rechazados)
    if ($isAdmin) {
        $stmt = $pdo->query("SELECT COUNT(*) as activos FROM Prestamo WHERE estado IN ('Solicitado', 'Aprobado', 'Entregado')");
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) as activos FROM Prestamo WHERE id_usuario = ? AND estado IN ('Solicitado', 'Aprobado', 'Entregado')");
        $stmt->execute([$id]);
    }
    $stats['prestamos'] = $stmt->fetch();
    
    responder("success", "Métricas del Dashboard", $stats);
}

responder("error", "Método no permitido.", null, 405);
?>
