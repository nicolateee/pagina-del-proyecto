<?php
declare(strict_types=1);

// ============================================================
//  CAPA DE NEGOCIO / DATOS: Inventario
// ============================================================
class InventarioModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function listar(): array
    {
        $stmt = $this->db->query('SELECT * FROM inventario ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function agregar(array $d): int
    {
        $sql = 'INSERT INTO inventario
                (nombre, tipo, marca, modelo, numero_serie, estado, ubicacion, descripcion, cantidad)
                VALUES
                (:nombre, :tipo, :marca, :modelo, :numero_serie, :estado, :ubicacion, :descripcion, :cantidad)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($this->normalizar($d));
        return (int) $this->db->lastInsertId();
    }

    public function editar(int $id, array $d): bool
    {
        $sql = 'UPDATE inventario SET
                nombre = :nombre, tipo = :tipo, marca = :marca, modelo = :modelo,
                numero_serie = :numero_serie, estado = :estado, ubicacion = :ubicacion,
                descripcion = :descripcion, cantidad = :cantidad
                WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $datos = $this->normalizar($d);
        $datos['id'] = $id;
        return $stmt->execute($datos);
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM inventario WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    private function normalizar(array $d): array
    {
        $estadosValidos = ['activo', 'en_reparacion', 'de_baja'];
        $estado = in_array($d['estado'] ?? '', $estadosValidos, true) ? $d['estado'] : 'activo';

        return [
            'nombre'       => trim((string) $d['nombre']),
            'tipo'         => !empty($d['tipo']) ? trim((string) $d['tipo']) : null,
            'marca'        => !empty($d['marca']) ? trim((string) $d['marca']) : null,
            'modelo'       => !empty($d['modelo']) ? trim((string) $d['modelo']) : null,
            'numero_serie' => !empty($d['numero_serie']) ? trim((string) $d['numero_serie']) : null,
            'estado'       => $estado,
            'ubicacion'    => !empty($d['ubicacion']) ? trim((string) $d['ubicacion']) : null,
            'descripcion'  => !empty($d['descripcion']) ? trim((string) $d['descripcion']) : null,
            'cantidad'     => max(1, (int) ($d['cantidad'] ?? 1)),
        ];
    }
}
