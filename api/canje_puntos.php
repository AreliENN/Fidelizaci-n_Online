<?php
// $pdo, $id, $method vienen de api.php

switch ($method) {
  case 'GET':
    if ($id) {
      $stmt = $pdo->prepare('SELECT * FROM canje_puntos WHERE id_bonificacion = ?');
      $stmt->execute([$id]);
      echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    } else {
      $rows = $pdo
        ->query(
          'SELECT r.id_canje,
                  c.nombre, c.apellidos,
                  p.nombre AS premio,
                  r.puntos_usados,
                  r.puntos_restantes,
                  r.fecha
           FROM canje_puntos r
           JOIN cliente c ON r.id_cliente = c.id_cliente
           JOIN premios   p ON r.id_premio  = p.id_premio
           ORDER BY r.fecha DESC'
        )
        ->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode($rows);
    }
    break;

  case 'POST':
    $d = json_decode(file_get_contents('php://input'), true);
    // calcular puntos restantes
    $cur = $pdo->prepare('SELECT puntos FROM cliente WHERE id_cliente = ?');
    $cur->execute([$d['id_cliente']]);
    $current = (int)$cur->fetchColumn();
    $used    = (int)$d['puntos_usados'];
    $rem     = $current - $used;

    // insertar
    $ins = $pdo->prepare(
      'INSERT INTO canje_puntos
         (id_cliente, id_premio, puntos_usados, puntos_restantes)
       VALUES (?, ?, ?, ?)'
    );
    $ins->execute([$d['id_cliente'], $d['id_premio'], $used, $rem]);

    // actualizar cliente
    $pdo->prepare(
      'UPDATE cliente SET puntos = ? WHERE id_cliente = ?'
    )->execute([$rem, $d['id_cliente']]);

    http_response_code(201);
    echo json_encode(['id'=>$pdo->lastInsertId()]);
    break;

  case 'PUT':
  case 'PATCH':
    if (!$id) { http_response_code(400); exit; }
    $d = json_decode(file_get_contents('php://input'), true);
    // recalcular
    $cur = $pdo->prepare('SELECT puntos FROM cliente WHERE id_cliente = ?');
    $cur->execute([$d['id_cliente']]);
    $current = (int)$cur->fetchColumn();
    $used    = (int)$d['puntos_usados'];
    $rem     = $current - $used;

    // actualizar redención
    $pdo->prepare(
      'UPDATE canje_puntos
         SET id_cliente=?, id_premio=?, puntos_usados=?, puntos_restantes=?
       WHERE id_canje=?'
    )->execute([$d['id_cliente'],$d['id_premio'],$used,$rem,$id]);

    // actualizar cliente
    $pdo->prepare(
      'UPDATE cliente SET puntos = ? WHERE id_cliente = ?'
    )->execute([$rem, $d['id_cliente']]);

    echo json_encode(['updated'=>$id]);
    break;

  case 'DELETE':
    if (!$id) { http_response_code(400); exit; }
    // opcionalmente restaurar puntos antes de borrar…
    $pdo->prepare('DELETE FROM canje_puntos WHERE id_canje = ?')
        ->execute([$id]);
    echo json_encode(['deleted'=>$id]);
    break;

  default:
    http_response_code(405);
    header('Allow: GET,POST,PUT,DELETE');
}
