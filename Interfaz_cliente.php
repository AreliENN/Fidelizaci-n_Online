<?php 
session_start();
// Verificar sesión de cliente
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: Login.php');
    exit;
}
$id_cliente = $_SESSION['user_id'];

$host = 'localhost';
$db   = 'fidelizacion';
$user = 'root';
$pass = '';
$pdo  = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Creación de la tarjeta digital
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_type'] ?? '') === 'create_card') {
    $telefono_input = trim($_POST['telefono_movil']);

    $stmt = $pdo->prepare("SELECT telefono_movil FROM cliente WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    $telefono_bd = $stmt->fetchColumn();

    if ($telefono_input !== $telefono_bd) {
        $error = "El número de teléfono no coincide.";
    } else {
        do {
            $numero = '';
            for ($i = 0; $i < 16; $i++) {
                $numero .= rand(0, 9);
            }
            $chk = $pdo->prepare("SELECT 1 FROM tarjeta WHERE numero_tarjeta = ?");
            $chk->execute([$numero]);
        } while ($chk->fetch());

        do {
            $cvv = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
            $chk = $pdo->prepare("SELECT 1 FROM tarjeta WHERE cvv = ?");
            $chk->execute([$cvv]);
        } while ($chk->fetch());

        $fecha_vto = date('Y-m-d', strtotime('+4 years'));

        $pdo->beginTransaction();
        $pdo->prepare("
            INSERT INTO tarjeta (id_cliente, numero_tarjeta, fecha_vencimiento, cvv)
            VALUES (?, ?, ?, ?)
        ")->execute([$id_cliente, $numero, $fecha_vto, $cvv]);

        $pdo->prepare("
            UPDATE cliente
               SET tarjeta_digital = 'si'
             WHERE id_cliente = ?
        ")->execute([$id_cliente]);
        $pdo->commit();

        header('Location: Interfaz_cliente.php');
        exit;
    }
}

// Obtener datos
$stmt = $pdo->prepare("
    SELECT
  c.nombre,
  c.apellidos,
  c.puntos,
  c.tarjeta_digital,
  c.imagen,
  t.numero_tarjeta,
  t.fecha_vencimiento,
  t.cvv

    FROM cliente c
    LEFT JOIN tarjeta t ON t.id_cliente = c.id_cliente
    WHERE c.id_cliente = ?
");
$stmt->execute([$id_cliente]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Mi Cuenta - Fidelización</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #eef2f5; }
    .card-virtual {
      max-width: 380px;
      margin: auto;
      padding: 1.5rem;
      border-radius: 16px;
      background: #FFA500;
      color: #000;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      position: relative;
    }
    .card-number {
      font-size: 1.3rem;
      letter-spacing: 2px;
      margin: 1rem 0;
      font-weight: bold;
    }
    .section {
      text-align: center;
      margin: 2rem 0;
    }
  </style>
</head>
<body>
<div class="container py-4">
  <nav class="navbar navbar-light bg-light">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h1">Mi Cuenta</span>
      <a href="Cerrar_sesion.php" class="btn btn-outline-danger">Cerrar sesión</a>
    </div>
  </nav>

  <?php if ($u['tarjeta_digital'] !== 'si'): ?>
    <!-- Formulario: Creacion de tarjeta -->
    <div class="modal" id="modalConfirm" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Generar Tarjeta Digital</h5></div>
          <div class="modal-body">
            <p>Crea tu tarjeta digital para acceder a la app.</p>
          </div>
          <div class="modal-footer">
            <button type="button" id="btnAccept" class="btn btn-primary">Aceptar</button>
            <a href="logout.php" class="btn btn-secondary">Rechazar</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Formulario: Registro de telefono -->
    <div class="modal" id="modalPhone" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form method="POST">
            <input type="hidden" name="form_type" value="create_card">
            <div class="modal-header"><h5 class="modal-title">Verificar Teléfono</h5></div>
            <div class="modal-body">
              <p>Escribe tu número de teléfono registrado:</p>
              <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
              <?php endif; ?>
              <input type="text" name="telefono_movil" class="form-control" required>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Generar Tarjeta</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  <?php else: ?>
    <div class="text-center mb-4">
      <h2>Bienvenid@, <?= htmlspecialchars($u['nombre'].' '.$u['apellidos']) ?></h2>
      
    </div>
<?php if (!empty($u['imagen'])): ?>
  <div class="text-center mb-3">
    <img src="<?= htmlspecialchars($u['imagen']) ?>" ...
         alt="Foto del cliente"
         class="rounded-circle shadow"
         style="width: 140px; height: 140px; object-fit: cover; border: 3px solid #ccc;">
  </div>
<?php endif; ?>

    <!-- Diseño de tarjeta digital -->
    <div class="card-virtual mb-4 text-center">
      <div class="mb-2">
        <strong>Tarjeta</strong><br>
        <span class="card-number"><?= chunk_split($u['numero_tarjeta'], 4, ' ') ?></span>
      </div>
      <div class="mb-3">
        <img src="img/logo.png" alt="DigitalFi Bank" style="width: 20%; max-width: 250px; border-radius: 10px;">
      </div>
      <div>
        <a href="#" class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#detallesCard">Detalle de tarjeta</a>
      </div>
    </div>

    <!-- Modal detalles Tarjeta-->
    <div class="modal fade" id="detallesCard" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Detalles de tu Tarjeta</h5></div>
          <div class="modal-body text-center">
            <p><strong>Número:</strong> <?= chunk_split($u['numero_tarjeta'], 4, ' ') ?></p>
            <p><strong>Fecha de vencimiento:</strong> <?= date('m/Y', strtotime($u['fecha_vencimiento'])) ?></p>
            <p><strong>CVV:</strong> <?= htmlspecialchars($u['cvv']) ?></p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" data-bs-dismiss="modal">Vale</button>
          </div>
        </div>
      </div>
    </div>
<!-- Puntos disponibles -->
<div class="text-center mb-4">
  <h5 style="color: #444;">Puntos acumulados</h5>
  <p class="display-6 fw-bold"><?= number_format($u['puntos'], 1) ?> puntos</p>
</div>
    <!-- Premios y beneficios -->
<div class="d-flex justify-content-around bg-white py-3 rounded shadow-sm mb-4">
  <div class="text-center">
    <div style="font-size: 1.2rem; font-weight: bold;">Premios</div>
    <div style="font-size: 1.5rem;" class="mt-2">
      <a href="Premios_usuario.php" class="btn btn-outline-success btn-sm">Ver</a>
    </div>
  </div>
  <div class="text-center border-start ps-3">
    <div style="font-size: 1.2rem; font-weight: bold;">Beneficios</div>
    <div style="font-size: 1.5rem;" class="mt-2">
      <a href="Beneficio_usuario.php" class="btn btn-outline-warning btn-sm">Ver</a>
    </div>
  </div>
</div>

  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const hasCard = <?= json_encode($u['tarjeta_digital'] === 'si') ?>;
  if (!hasCard) {
    const modalConfirm = new bootstrap.Modal(document.getElementById('modalConfirm'), { backdrop: 'static', keyboard: false });
    modalConfirm.show();

    document.getElementById('btnAccept').addEventListener('click', () => {
      modalConfirm.hide();
      const modalPhone = new bootstrap.Modal(document.getElementById('modalPhone'), { backdrop: 'static', keyboard: false });
      modalPhone.show();
    });
  }
});
</script>
<script>
document.addEventListener("DOMContentLoaded", () => {

  // Función para mostrar notificación
  function mostrarNotificacion(titulo, mensaje) {
    if (!("Notification" in window)) {
      console.warn("Este navegador no soporta notificaciones.");
      return;
    }

    if (Notification.permission === "granted") {
      const opciones = {
        body: mensaje,
        icon: "img/logo.png"
      };
      const notificacion = new Notification(titulo, opciones);
      setTimeout(() => notificacion.close(), 5000);
    } else if (Notification.permission === "default") {
      Notification.requestPermission().then(permiso => {
        if (permiso === "granted") mostrarNotificacion(titulo, mensaje);
      });
    } else {
      console.log("El usuario no aceptó recibir notificaciones.");
    }
  }

  // Notificación inicial si se acaba de crear tarjeta digital
  const tarjetaCreada = <?= json_encode($u['tarjeta_digital'] === 'si') ?>;
  if (tarjetaCreada) {
    mostrarNotificacion("¡Tarjeta Digital Lista!", "Tu tarjeta digital ha sido generada correctamente. ¡Comienza a acumular puntos!");
  }

  // Ejemplo: notificación de puntos acumulados
  const puntos = <?= json_encode($u['puntos']) ?>;
  if (puntos > 0) {
    mostrarNotificacion("Tus puntos", `Tienes ${puntos} puntos acumulados.`);
  }

  // Opcional: botón para probar notificación
  const btnTestNotif = document.createElement("button");
  btnTestNotif.textContent = "Probar Notificación";
  btnTestNotif.className = "btn btn-sm btn-outline-info mt-3";
  document.querySelector(".container")?.appendChild(btnTestNotif);
  btnTestNotif.addEventListener("click", () => {
    mostrarNotificacion("Notificación de prueba", "Esta es una notificación de prueba en tu cuenta.");
  });

});
</script>

</body>
</html>
