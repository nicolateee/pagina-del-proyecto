<?php
declare(strict_types=1);

// ============================================================
//  CAPA DE NEGOCIO / DATOS: Incidencia
// ============================================================
class IncidenciaModel
{
    private PDO $db;
    private const ESTADOS_VALIDOS = ['abierta', 'en_proceso', 'resuelta', 'cerrada'];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Admin y técnico ven todas las incidencias.
     * Solicitante y usuario solo ven las que ellos reportaron.
     */
    public function listar(array $usuario): array
    {
        $base = 'SELECT i.*, ur.nombre AS nombre_reportador, ut.nombre AS nombre_tecnico
                  FROM incidencias i
                  LEFT JOIN usuarios ur ON i.reportado_por = ur.id
                  LEFT JOIN usuarios ut ON i.asignado_a = ut.id';

        if (in_array($usuario['rol'], ['admin', 'tecnico'], true)) {
            $stmt = $this->db->query($base . ' ORDER BY i.fecha_creacion DESC');
            return $stmt->fetchAll();
        }

        $stmt = $this->db->prepare($base . ' WHERE i.reportado_por = :uid ORDER BY i.fecha_creacion DESC');
        $stmt->execute(['uid' => $usuario['id']]);
        return $stmt->fetchAll();
    }

    public function crear(array $d, int $reportadoPor): int
    {
        $prioridadesValidas = ['alta', 'media', 'baja'];
        $prioridad = in_array($d['prioridad'] ?? '', $prioridadesValidas, true) ? $d['prioridad'] : 'media';
        $equipoId = !empty($d['equipo_id']) ? (int) $d['equipo_id'] : null;

        $stmt = $this->db->prepare(
            'INSERT INTO incidencias (titulo, descripcion, area, equipo_id, prioridad, reportado_por)
             VALUES (:titulo, :descripcion, :area, :equipo_id, :prioridad, :reportado_por)'
        );
        $stmt->execute([
            'titulo'        => trim((string) $d['titulo']),
            'descripcion'   => trim((string) $d['descripcion']),
            'area'          => trim((string) $d['area']),
            'equipo_id'     => $equipoId,
            'prioridad'     => $prioridad,
            'reportado_por' => $reportadoPor,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, string $estado, ?int $asignadoA = null): bool
    {
        if (!in_array($estado, self::ESTADOS_VALIDOS, true)) {
            return false;
        }
        $fechaResolucion = in_array($estado, ['resuelta', 'cerrada'], true) ? date('Y-m-d H:i:s') : null;

        if ($asignadoA !== null) {
            $stmt = $this->db->prepare(
                'UPDATE incidencias SET estado = :estado, asignado_a = :asignado_a, fecha_resolucion = :fr WHERE id = :id'
            );
            return $stmt->execute(['estado' => $estado, 'asignado_a' => $asignadoA, 'fr' => $fechaResolucion, 'id' => $id]);
        }

        $stmt = $this->db->prepare(
            'UPDATE incidencias SET estado = :estado, fecha_resolucion = :fr WHERE id = :id'
        );
        return $stmt->execute(['estado' => $estado, 'fr' => $fechaResolucion, 'id' => $id]);
    }
}
