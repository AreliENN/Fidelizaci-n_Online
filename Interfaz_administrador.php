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
  <style>
    .dashboard-item a { text-decoration: none; }
    .card-dashboard {
      padding: 2rem;
      border-radius: 12px;
      color: white;
      text-align: center;
      transition: transform 0.2s ease;
    }
    .card-dashboard:hover {
      transform: scale(1.05);
    }
    .bg-clientes { background-color: #007bff; }
    .bg-puntos { background-color: #28a745; }
    .bg-premios { background-color: #ffc107; color: #000; }
    .bg-beneficios { background-color: #6f42c1; }
  </style>
</head>
<body class="bg-light">

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <div class="container-fluid justify-content-between">
      <h1 class="text-white h3 my-2">Panel de Control del Administrador</h1>
      <a href="Cerrar_sesion.php" class="btn btn-outline-danger">Cerrar Sesión</a>
    </div>
  </nav>

  <!-- Modulos -->
  <main class="container py-5">
    <div class="row g-4">
      <div class="col-12 col-md-6 col-lg-3">
        <div class="dashboard-item">
          <a href="Modulo_Cliente.php">
            <div class="card card-dashboard bg-clientes">
              <h5 class="card-title">Clientes</h5>
            </div>
          </a>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <div class="dashboard-item">
          <a href="Modulo_Puntos.php">
            <div class="card card-dashboard bg-puntos">
              <h5 class="card-title">Alta_Puntos</h5>
            </div>
          </a>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <div class="dashboard-item">
          <a href="Modulo_Premios.php">
            <div class="card card-dashboard bg-premios">
              <h5 class="card-title">Premios</h5>
            </div>
          </a>
        </div>
      </div>
      <div class="col-12 col-md-6 col-lg-3">
        <div class="dashboard-item">
          <a href="Modulo_Beneficios.php">
            <div class="card card-dashboard bg-beneficios">
              <h5 class="card-title">Beneficios</h5>
            </div>
          </a>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
