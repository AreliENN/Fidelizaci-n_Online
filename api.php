<?php


// Conexión a BD
$pdo = new PDO(
    'mysql:host=localhost;dbname=fidelizacion;charset=utf8mb4',
    'root', '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Parsear URL
$path     = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$resource = array_shift($path) ?: '';
$id       = array_shift($path) ?: null;
$method   = $_SERVER['REQUEST_METHOD'];

switch ($resource) {
    case 'clientes':
        require __DIR__ . '/api/clientes.php';
        break;
    case 'bonificaciones':
        require __DIR__ . '/api/bonificaciones.php';
        break;
    case 'canje_puntos':
        require __DIR__ . '/api/canje_puntos.php';
        break;
    case 'premios':
        require __DIR__ . '/api/premios.php';
        break;
    case 'beneficios':
        require __DIR__ . '/api/beneficios.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['error'=>'Recurso no encontrado']);
}
