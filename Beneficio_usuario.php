<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: Login.php');
    exit;
}
$id_cliente = $_SESSION['user_id'];
// Conexión de la base de datos
$pdo = new PDO(
    "mysql:host=localhost;dbname=fidelizacion;charset=utf8mb4",
    "root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Obtener beneficios disponibles
$stmt = $pdo->query("SELECT id_beneficio, nombre_empresa, descripcion, descuento, vigencia_de, vigencia_hasta, imagen FROM beneficios WHERE activo = 1");
$beneficios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Beneficios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card-beneficio {
      cursor: pointer;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: transform 0.2s;
    }
    .card-beneficio:hover {
      transform: scale(1.03);
    }
    .card-img-top {
      height: 150px;
      object-fit: cover;
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
    }
  </style>
</head>
<body>
  <div class="header p-3">
    <a href="Interfaz_cliente.php" class="btn btn-outline-secondary">Regresar</a>
  </div>

  <div class="container py-4">
    <h2 class="mb-4 text-center">Tus Beneficios</h2>

    <div class="row g-3" id="accordionBeneficios">
      <?php foreach($beneficios as $b): ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card card-beneficio">
            <img src="<?= htmlspecialchars($b['imagen']) ?>" class="card-img-top" alt="<?= htmlspecialchars($b['nombre_empresa']) ?>">
            <div class="card-body text-center">
              <h5 class="card-title mb-2"><?= htmlspecialchars($b['nombre_empresa']) ?></h5>
              <button class="btn btn-sm btn-outline-primary toggle-btn"
                      data-bs-toggle="collapse"
                      data-bs-target="#detalle<?= $b['id_beneficio'] ?>"
                      aria-expanded="false"
                      aria-controls="detalle<?= $b['id_beneficio'] ?>">
                Ver Detalles
              </button>
            </div>
            <div id="detalle<?= $b['id_beneficio'] ?>" 
                 class="collapse" 
                 data-bs-parent="#accordionBeneficios">
              <div class="card-body text-start">
                <p><strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($b['descripcion'])) ?></p>
                <p><strong>Descuento:</strong> <?= htmlspecialchars($b['descuento']) ?></p>
                <p><strong>Vigencia:</strong><br>Desde <?= date('d/m/Y', strtotime($b['vigencia_de'])) ?> hasta <?= date('d/m/Y', strtotime($b['vigencia_hasta'])) ?></p>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Cambiar texto del botón al expandir/contraer
    document.querySelectorAll('.toggle-btn').forEach(btn => {
      const targetId = btn.getAttribute('data-bs-target');
      const target = document.querySelector(targetId);
      const collapse = new bootstrap.Collapse(target, { toggle: false });

      btn.addEventListener('click', () => {
        if (target.classList.contains('show')) {
          collapse.hide();
          btn.textContent = 'Ver Detalles';
        } else {
          // Ocultar otros botones
          document.querySelectorAll('.collapse.show').forEach(openEl => {
            const openBtn = document.querySelector(`button[data-bs-target="#${openEl.id}"]`);
            if (openBtn) openBtn.textContent = 'Ver Detalles';
          });
          collapse.show();
          btn.textContent = 'Ocultar Detalles';
        }
      });

      // Detectar cuando se oculta para cambiar el texto
      target.addEventListener('hidden.bs.collapse', () => {
        btn.textContent = 'Ver Detalles';
      });
      target.addEventListener('shown.bs.collapse', () => {
        btn.textContent = 'Ocultar Detalles';
      });
    });
  </script>
</body>
</html>
