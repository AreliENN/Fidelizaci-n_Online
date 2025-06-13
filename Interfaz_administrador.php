<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
  header('Location: Login.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel de Administración</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body {
      background: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .navbar {
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .card-dashboard {
      padding: 2rem 1rem;
      border-radius: 16px;
      color: white;
      text-align: center;
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
      transition: all 0.3s ease-in-out;
    }
    .card-dashboard:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    .card-dashboard h5 {
      margin-top: 1rem;
      font-weight: 600;
      font-size: 1.2rem;
    }
    .card-dashboard i {
      font-size: 2.5rem;
    }
    .bg-clientes {
      background: linear-gradient(135deg, #0d6efd, #5a8dee);
    }
    .bg-puntos {
      background: linear-gradient(135deg, #198754, #43c59e);
    }
    .bg-premios {
      background: linear-gradient(135deg, #ffc107, #ffda59);
      color: #000;
    }
    .bg-beneficios {
      background: linear-gradient(135deg, #6f42c1, #a774e8);
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <div class="container-fluid justify-content-between">
      <h1 class="text-white h4 my-2">🎛️ Panel de Control del Administrador</h1>
      <a href="Cerrar_sesion.php" class="btn btn-outline-danger">Cerrar Sesión</a>
    </div>
  </nav>

  <main class="container py-5">
    <div class="row g-4">
      <div class="col-12 col-md-6 col-lg-3">
        <a href="Modulo_Cliente.php" class="text-decoration-none">
          <div class="card-dashboard bg-clientes">
            <i class="bi bi-people-fill"></i>
            <h5>Clientes</h5>
          </div>
        </a>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <a href="Modulo_Puntos.php" class="text-decoration-none">
          <div class="card-dashboard bg-puntos">
            <i class="bi bi-stars"></i>
            <h5>Alta Puntos</h5>
          </div>
        </a>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <a href="Modulo_Premios.php" class="text-decoration-none">
          <div class="card-dashboard bg-premios">
            <i class="bi bi-gift-fill"></i>
            <h5>Premios</h5>
          </div>
        </a>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <a href="Modulo_Beneficios.php" class="text-decoration-none">
          <div class="card-dashboard bg-beneficios">
            <i class="bi bi-stars"></i>
            <h5>Beneficios</h5>
          </div>
        </a>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
