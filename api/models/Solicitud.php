<?php
declare(strict_types=1);

// ============================================================
//  CAPA DE NEGOCIO / DATOS: Solicitud de servicio
// ============================================================
class SolicitudModel
{
    private PDO $db;
    private const ESTADOS_VALIDOS = ['pendiente', 'en_proceso', 'completada', 'rechazada'];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Admin ve todas. Solicitante ve las propias. Técnico ve las asignadas a él.
     * Rol 'usuario' no tiene acceso a este módulo.
     */
    public function listar(array $usuario): array
    {
        $base = 'SELECT s.*, us.nombre AS nombre_solicitante, ut.nombre AS nombre_tecnico
                  FROM solicitudes s
                  LEFT JOIN usuarios us ON s.solicitante_id = us.id
                  LEFT JOIN usuarios ut ON s.tecnico_id = ut.id';

        switch ($usuario['rol']) {
            case 'admin':
                $stmt = $this->db->query($base . ' ORDER BY s.fecha_creacion DESC');
                return $stmt->fetchAll();

            case 'tecnico':
                $stmt = $this->db->prepare($base . ' WHERE s.tecnico_id = :uid ORDER BY s.fecha_creacion DESC');
                $stmt->execute(['uid' => $usuario['id']]);
                return $stmt->fetchAll();

            case 'solicitante':
                $stmt = $this->db->prepare($base . ' WHERE s.solicitante_id = :uid ORDER BY s.fecha_creacion DESC');
                $stmt->execute(['uid' => $usuario['id']]);
                return $stmt->fetchAll();

            default:
                return [];
        }
    }

    public function crear(array $d, int $solicitanteId): int
    {
        $prioridadesValidas = ['alta', 'media', 'baja'];
        $prioridad = in_array($d['prioridad'] ?? '', $prioridadesValidas, true) ? $d['prioridad'] : 'media';

        $stmt = $this->db->prepare(
            'INSERT INTO solicitudes (tipo_servicio, descripcion, prioridad, solicitante_id)
             VALUES (:tipo, :descripcion, :prioridad, :solicitante_id)'
        );
        $stmt->execute([
            'tipo'           => trim((string) $d['tipo_servicio']),
            'descripcion'    => trim((string) $d['descripcion']),
            'prioridad'      => $prioridad,
            'solicitante_id' => $solicitanteId,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, string $estado, ?int $tecnicoId = null): bool
    {
        if (!in_array($estado, self::ESTADOS_VALIDOS, true)) {
            return false;
        }
        $fechaCompletada = $estado === 'completada' ? date('Y-m-d H:i:s') : null;

        if ($tecnicoId !== null) {
            $stmt = $this->db->prepare(
                'UPDATE solicitudes SET estado = :estado, tecnico_id = :tid, fecha_completada = :fc WHERE id = :id'
            );
            return $stmt->execute(['estado' => $estado, 'tid' => $tecnicoId, 'fc' => $fechaCompletada, 'id' => $id]);
        }

        $stmt = $this->db->prepare(
            'UPDATE solicitudes SET estado = :estado, fecha_completada = :fc WHERE id = :id'
        );
        return $stmt->execute(['estado' => $estado, 'fc' => $fechaCompletada, 'id' => $id]);
    }
}
