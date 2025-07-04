<?php
// $pdo, $id, $method vienen de api.php

switch ($method) {
  case 'GET':
    if ($id) {
      $stmt = $pdo->prepare('SELECT * FROM beneficios WHERE id_beneficio = ?');
      $stmt->execute([$id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$row) {
        http_response_code(404);
        echo json_encode(['error'=>'No existe']);
      } else {
        echo json_encode($row);
      }
    } else {
      $rows = $pdo->query('SELECT * FROM beneficios ORDER BY nombre_empresa')
                  ->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode($rows);
    }
    break;

  case 'POST':
    // Multipart form: $_POST + $_FILES
    $empresa = $_POST['nombre_empresa'] ?? null;
    $descripcion   = $_POST['descripcion'] ?? null;
    $descuento     = $_POST['descuento'] ?? null;
    $vd            = $_POST['vigencia_de'] ?? null;
    $vh            = $_POST['vigencia_hasta'] ?? null;
    $activo        = isset($_POST['activo']) ? 1 : 0;

    // Procesar imagen
    $imgPath = null;
    if (!empty($_FILES['imagen']['tmp_name'])) {
      $dest = 'img/beneficios/' . uniqid('ben_') . '_' . basename($_FILES['imagen']['name']);
      move_uploaded_file($_FILES['imagen']['tmp_name'], __DIR__ . '/../' . $dest);
      $imgPath = $dest;
    }

    $stmt = $pdo->prepare(
      'INSERT INTO beneficios 
       (nombre_empresa,descripcion,descuento,vigencia_de,vigencia_hasta,activo,imagen)
       VALUES (?,?,?,?,?,?,?)'
    );
    $stmt->execute([
      $empresa, $descripcion, $descuento,
      $vd, $vh, $activo, $imgPath
    ]);
    http_response_code(201);
    echo json_encode(['id'=>$pdo->lastInsertId()]);
    break;

  case 'PUT':
  case 'PATCH':
    if (!$id) { http_response_code(400); exit; }
    // parse the raw multipart body
    parse_str(file_get_contents('php://input'), $d);
    $fields = ['nombre_empresa','descripcion','descuento','vigencia_de','vigencia_hasta','activo','imagen'];
    $sets = $vals = [];
    foreach ($fields as $f) {
      if (isset($d[$f])) {
        $sets[] = "$f = ?";
        $vals[] = $d[$f];
      }
    }
    if (empty($sets)) { http_response_code(400); exit; }
    $vals[] = $id;
    $sql = 'UPDATE beneficios SET ' . implode(',', $sets) . ' WHERE id_beneficio = ?';
    $pdo->prepare($sql)->execute($vals);
    echo json_encode(['updated'=>$id]);
    break;

  case 'DELETE':
    if (!$id) { http_response_code(400); exit; }
    $pdo->prepare('DELETE FROM beneficios WHERE id_beneficio = ?')
        ->execute([$id]);
    echo json_encode(['deleted'=>$id]);
    break;

  default:
    http_response_code(405);
    header('Allow: GET,POST,PUT,PATCH,DELETE');
}
