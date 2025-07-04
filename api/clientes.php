<?php
// Conexión a BD
$dsn = "mysql:host=localhost;dbname=fidelizacion;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Error BD: " . $e->getMessage());
}

// El $id lo definimos en api.php a partir de PATH_INFO
global $id;
$method = $_SERVER['REQUEST_METHOD'];

// CRUD de clientes
switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare('SELECT * FROM cliente WHERE id_cliente = ?');
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                http_response_code(404);
                echo json_encode(['error' => 'No existe']);
            } else {
                echo json_encode($row);
            }
        } else {
            $rows = $pdo
                ->query('SELECT * FROM cliente ORDER BY id_cliente')
                ->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($rows);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $fields = ['nombre', 'apellidos', 'telefono_movil', 'direccion', 'correo_electronico', 'estado', 'ciudad', 'tarjeta_digital'];
        $placeholders = implode(',', array_fill(0, count($fields), '?'));
        $sql = 'INSERT INTO cliente (' . implode(',', $fields) . ') VALUES (' . $placeholders . ')';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_map(fn($f) => $data[$f] ?? null, $fields));
        http_response_code(201);
        echo json_encode(['id' => $pdo->lastInsertId()]);
        break;

    case 'PUT':
    case 'PATCH':
        if (!$id) {
            http_response_code(400);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $fields = ['nombre', 'apellidos', 'telefono_movil', 'direccion', 'correo_electronico', 'estado', 'ciudad', 'tarjeta_digital'];
        $sets = $vals = [];
        foreach ($fields as $f) {
            if (isset($data[$f])) {
                $sets[] = "$f = ?";
                $vals[] = $data[$f];
            }
        }
        if (empty($sets)) {
            http_response_code(400);
            exit;
        }
        $vals[] = $id;
        $sql = 'UPDATE cliente SET ' . implode(',', $sets) . ' WHERE id_cliente = ?';
        $pdo->prepare($sql)->execute($vals);
        echo json_encode(['updated' => $id]);
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            exit;
        }
        $pdo->prepare('DELETE FROM cliente WHERE id_cliente = ?')
            ->execute([$id]);
        echo json_encode(['deleted' => $id]);
        break;

    default:
        http_response_code(405);
        header('Allow: GET,POST,PUT,PATCH,DELETE');
}
