<?php
// $pdo, $id, $method vienen de api.php

switch ($method) {
  case 'GET':
    if ($id) {
      $stmt = $pdo->prepare('SELECT * FROM premios WHERE id_premio = ?');
      $stmt->execute([$id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$row) {
        http_response_code(404);
        echo json_encode(['error'=>'No existe']);
      } else {
        echo json_encode($row);
      }
    } else {
      $rows = $pdo->query('SELECT * FROM premios ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode($rows);
    }
    break;

  case 'POST':
    $d = json_decode(file_get_contents('php://input'), true);
    $fields = ['nombre','descripcion','puntos_requeridos','disponibles','activo','imagen'];
    $ph = implode(',', array_fill(0, count($fields), '?'));
    $sql = 'INSERT INTO premios (' . implode(',', $fields) . ') VALUES (' . $ph . ')';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_map(fn($f) => $d[$f] ?? null, $fields));
    http_response_code(201);
    echo json_encode(['id' => $pdo->lastInsertId()]);
    break;

  case 'PUT':
  case 'PATCH':
    if (!$id) { http_response_code(400); exit; }
    $d = json_decode(file_get_contents('php://input'), true);
    $fields = ['nombre','descripcion','puntos_requeridos','disponibles','activo','imagen'];
    $sets = $vals = [];
    foreach ($fields as $f) {
      if (isset($d[$f])) {
        $sets[]  = "$f = ?";
        $vals[]  = $d[$f];
      }
    }
    if (empty($sets)) { http_response_code(400); exit; }
    $vals[] = $id;
    $sql = 'UPDATE premios SET ' . implode(',', $sets) . ' WHERE id_premio = ?';
    $pdo->prepare($sql)->execute($vals);
    echo json_encode(['updated' => $id]);
    break;

  case 'DELETE':
    if (!$id) { http_response_code(400); exit; }
    $pdo->prepare('DELETE FROM premios WHERE id_premio = ?')->execute([$id]);
    echo json_encode(['deleted' => $id]);
    break;

  default:
    http_response_code(405);
    header('Allow: GET,POST,PUT,PATCH,DELETE');
}
