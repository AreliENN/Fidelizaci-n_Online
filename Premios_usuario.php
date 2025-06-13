<?php
session_start();

// 1) Verificar sesión de cliente
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: Login.php');
    exit;
}
$id_cliente = $_SESSION['user_id'];

// Conexión
$pdo = new PDO(
    "mysql:host=localhost;dbname=fidelizacion;charset=utf8mb4",
    "root","", [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
);

// Obtener datos de cliente y tarjeta
$stmt = $pdo->prepare("
  SELECT c.puntos, t.numero_tarjeta
    FROM cliente c
    LEFT JOIN tarjeta t ON t.id_cliente = c.id_cliente
   WHERE c.id_cliente = ?
");
$stmt->execute([$id_cliente]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar compra de premio
$message = "";
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='buy') {
    $id_premio = (int)$_POST['id_premio'];

    // Detalles del premio
    $p = $pdo->prepare("SELECT puntos_requeridos, disponibles FROM premios WHERE id_premio = ? AND activo = 1");
    $p->execute([$id_premio]);
    $premio = $p->fetch(PDO::FETCH_ASSOC);

    if (!$premio) {
        $message = "Premio no encontrado.";
    } elseif ($u['puntos'] < $premio['puntos_requeridos']) {
        $message = "No cuentas con puntos suficientes.";
    } elseif ($premio['disponibles'] < 1) {
        $message = "Lo sentimos, este premio está agotado.";
    } else {
        // Canjeo de premios
        $pdo->beginTransaction();
        $inst = $pdo->prepare("
          INSERT INTO canje_puntos (id_cliente,id_premio,puntos_usados)
          VALUES (?,?,?)
        ");
        $inst->execute([$id_cliente, $id_premio, $premio['puntos_requeridos']]);

        $upd1 = $pdo->prepare("
          UPDATE cliente
             SET puntos = puntos - ?
           WHERE id_cliente = ?
        ");
        $upd1->execute([$premio['puntos_requeridos'], $id_cliente]);

        $upd2 = $pdo->prepare("
          UPDATE premios
             SET disponibles = disponibles - 1
           WHERE id_premio = ?
        ");
        $upd2->execute([$id_premio]);

        $pdo->commit();
        // Recarga datos de puntos
        $u['puntos'] -= $premio['puntos_requeridos'];
        $message = "¡Felicidades! Has obtenido tu premio.";
    }
}

// Mostrar premios activos
$stmt = $pdo->query("
  SELECT id_premio, nombre, descripcion, puntos_requeridos, disponibles, imagen
    FROM premios
   WHERE activo = 1
   ORDER BY puntos_requeridos ASC
");
$premios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Premios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { background: #f5f7fa; }
    .card-mini {
      padding: 1rem;
      border-radius: 12px;
      background:#FFA500;
      color: #fff;
      font-weight: 500;
      text-align: center;
    }
    .premio-card {
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      margin-bottom: 1rem;
    }
    .premio-card img {
      width: 100%;
      height: 140px;
      object-fit: cover;
    }
    .premio-body {
      padding: 1rem;
    }
  </style>
</head>
<body>
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <a href="Interfaz_cliente.php" class="btn btn-link">Regresar</a>
      <h2 class="m-0">Premios Disponibles</h2>
      <div style="width: 120px;"></div> 
    </div>

    <?php if($message): ?>
      <div class="alert alert-info text-center"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="row">
      <!-- Barra lateral con tarjeta -->
      <aside class="col-md-3 mb-4">
        <div class="card-mini">
          <small>Tarjeta nº</small><br>
          <?= chunk_split($u['numero_tarjeta']??'************0000',4,' ') ?><br>
          <small><?= $u['puntos'] ?> pts</small>
        </div>
      </aside>

      <!-- Lista de premios -->
      <section class="col-md-9">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
          <?php foreach($premios as $p): ?>
            <div class="col">
              <div class="premio-card">
                <img src="<?= htmlspecialchars($p['imagen'] ?: 'https://via.placeholder.com/300x140?text=Premio') ?>"
                     alt="<?= htmlspecialchars($p['nombre']) ?>" />
                <div class="premio-body">
                  <div class="fw-bold mb-2"><?= htmlspecialchars($p['nombre']) ?></div>
                  <div class="mb-3"><?= $p['puntos_requeridos'] ?> pts</div>
                  <form method="POST">
                    <input type="hidden" name="action" value="buy" />
                    <input type="hidden" name="id_premio" value="<?= $p['id_premio'] ?>" />
                    <button type="submit" class="btn btn-outline-primary w-100" <?= $p['disponibles'] < 1 ? 'disabled' : '' ?>>
                      <?= $p['disponibles'] < 1 ? 'Agotado' : 'Comprar' ?>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    </div>
  </div>
</body>
</html>
