<?php
declare(strict_types=1);

// ============================================================
//  CAPA DE DATOS: conexión PDO (Singleton) + capa transversal
//  de sesión y respuesta HTTP/JSON para todos los endpoints.
// ============================================================

session_start();

header('Content-Type: application/json; charset=utf-8');

// ⚠️ Ajustar credenciales según tu XAMPP (por defecto: root sin clave)
class Database
{
    private static ?PDO $conexion = null;

    public static function obtenerConexion(): PDO
    {
        if (self::$conexion === null) {
            $host    = 'localhost';
            $db      = 'sgrsi_db';
            $user    = 'root';
            $pass    = '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // fuerza prepared statements reales
            ];

            try {
                self::$conexion = new PDO($dsn, $user, $pass, $opciones);
            } catch (PDOException $e) {
                responder('error', 'Error de conexión a la base de datos', null, 500);
            }
        }
        return self::$conexion;
    }
}

/**
 * Contrato de respuesta único para toda la API: { status, message, data }
 */
function responder(string $status, string $message, $data = null, int $httpCode = 200): void
{
    http_response_code($httpCode);
    echo json_encode([
        'status'  => $status,   // 'exito' | 'error'
        'message' => $message,
        'data'    => $data,
    ]);
    exit;
}

function usuarioActual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function requerirSesion(): array
{
    $u = usuarioActual();
    if (!$u) {
        responder('error', 'No autenticado', null, 401);
    }
    return $u;
}

function requerirRol(array $rolesPermitidos): array
{
    $u = requerirSesion();
    if (!in_array($u['rol'], $rolesPermitidos, true)) {
        responder('error', 'No tiene permisos para realizar esta acción', null, 403);
    }
    return $u;
}
