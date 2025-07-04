<?php
// api/transacciones.php
// Asume $pdo, $id, $method vienen de api.php

switch($method) {
  case 'GET':
    if($id) {
      $stmt = $pdo->prepare(
        'SELECT * FROM bonificacion_puntos WHERE id_bonificacion = ?'
      );
      $stmt->execute([$id]);
      echo json_encode($stmt->fetch(PDO::FETCH_ASSOC)?:[]);
    } else {
      $rows = $pdo->query(
        'SELECT t.id_bonificacion, t.id_cliente, t.monto_compra, t.puntos_acreditados, t.fecha,
                c.nombre, c.apellidos
         FROM bonificacion_puntos t
         JOIN cliente c ON t.id_cliente = c.id_cliente
         ORDER BY t.fecha DESC'
      )->fetchAll(PDO::FETCH_ASSOC);
      echo json_encode($rows);
    }
    break;

  case 'POST':
  case 'PUT':
    $d = json_decode(file_get_contents('php://input'), true);
    $m = (float)($d['monto_compra']??0);
    $pts = floor($m/100)*5;

    if($method==='POST') {
      $ins = $pdo->prepare(
        'INSERT INTO bonificacion_puntos (id_cliente,monto_compra,puntos_acreditados)
         VALUES(?,?,?)'
      );
      $ins->execute([$d['id_cliente'],$m,$pts]);
      $newId = $pdo->lastInsertId();
      // sumar puntos
      $pdo->prepare(
        'UPDATE cliente SET puntos = puntos + ? WHERE id_cliente = ?'
      )->execute([$pts,$d['id_cliente']]);
      http_response_code(201);
      echo json_encode(['id'=>$newId]);
    } else {
      // restar puntos antiguos
      $o = $pdo->prepare(
        'SELECT puntos_acreditados,id_cliente FROM bonificacion_puntos WHERE id_bonificacion=?'
      );
      $o->execute([$id]);
      if($orig=$o->fetch(PDO::FETCH_ASSOC)) {
        $pdo->prepare(
          'UPDATE cliente SET puntos = puntos - ? WHERE id_cliente = ?'
        )->execute([$orig['puntos_acreditados'],$orig['id_cliente']]);
      }
      // actualizar transacción
      $upd = $pdo->prepare(
        'UPDATE bonificacion_puntos SET id_cliente=?, monto_compra=?, puntos_acreditados=? 
         WHERE id_bonificacion=?'
      );
      $upd->execute([$d['id_cliente'],$m,$pts,$id]);
      // sumar nuevos puntos
      $pdo->prepare(
        'UPDATE cliente SET puntos = puntos + ? WHERE id_cliente = ?'
      )->execute([$pts,$d['id_cliente']]);
      echo json_encode(['updated'=>$id]);
    }
    break;

  case 'DELETE':
    // restar puntos
    $o = $pdo->prepare(
      'SELECT puntos_acreditados,id_cliente FROM bonificacion_puntos WHERE id_bonificacion=?'
    );
    $o->execute([$id]);
    if($orig=$o->fetch(PDO::FETCH_ASSOC)) {
      $pdo->prepare(
        'UPDATE cliente SET puntos = puntos - ? WHERE id_cliente = ?'
      )->execute([$orig['puntos_acreditados'],$orig['id_cliente']]);
    }
    // borrar
    $pdo->prepare('DELETE FROM bonificacion_puntos WHERE id_bonificacion=?')
        ->execute([$id]);
    echo json_encode(['deleted'=>$id]);
    break;

  default:
    http_response_code(405);
    header('Allow: GET,POST,PUT,DELETE');
}
