<?php
declare(strict_types=1);


class Usuario
{
    private PDO $db;
    private const ROLES_VALIDOS = ['admin', 'solicitante', 'tecnico', 'usuario'];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function buscarPorCorreo(string $correo): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE correo = :correo LIMIT 1');
        $stmt->execute(['correo' => $correo]);
        $u = $stmt->fetch();
        return $u ?: null;
    }

    public function crear(string $nombre, string $correo, string $claveTexto, string $rol = 'usuario'): int
    {
        $hash = password_hash($claveTexto, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            'INSERT INTO usuarios (nombre, correo, clave, rol) VALUES (:nombre, :correo, :clave, :rol)'
        );
        $stmt->execute([
            'nombre' => $nombre,
            'correo' => $correo,
            'clave'  => $hash,
            'rol'    => $rol,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function verificarCredenciales(string $correo, string $claveTexto): ?array
    {
        $u = $this->buscarPorCorreo($correo);
        if (!$u || !(int) $u['activo']) {
            return null;
        }
        if (!password_verify($claveTexto, $u['clave'])) {
            return null;
        }
        return $u;
    }

    public function listarTodos(): array
    {
        $stmt = $this->db->query(
            'SELECT id, nombre, correo, rol, activo, fecha_creacion FROM usuarios ORDER BY id DESC'
        );
        return $stmt->fetchAll();
    }

    public function cambiarRol(int $id, string $nuevoRol): bool
    {
        if (!in_array($nuevoRol, self::ROLES_VALIDOS, true)) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE usuarios SET rol = :rol WHERE id = :id');
        return $stmt->execute(['rol' => $nuevoRol, 'id' => $id]);
    }

    public function activarDesactivar(int $id, bool $activo): bool
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET activo = :activo WHERE id = :id');
        return $stmt->execute(['activo' => $activo ? 1 : 0, 'id' => $id]);
    }
}
