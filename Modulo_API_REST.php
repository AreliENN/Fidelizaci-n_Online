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
  <meta charset="UTF-8">
  <title>Panel de Gestión - Plataforma de Recompensas</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Rubik:wght@400;700&display=swap');

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Rubik', sans-serif;
      background-color: #f0f3f8;
      color: #2c3e50;
    }

    header {
      background: linear-gradient(to right, #3498db, #6dd5fa);
      padding: 40px 20px;
      color: white;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      position: relative;
    }

    .logout-btn {
      position: absolute;
      top: 20px;
      right: 30px;
      background-color: #e74c3c;
      color: white;
      padding: 10px 18px;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      text-decoration: none;
      transition: background 0.3s ease;
    }

    .logout-btn:hover {
      background-color: #c0392b;
    }

    header h1 {
      font-size: 2.8rem;
      margin-bottom: 10px;
      text-align: center;
    }

    header p {
      text-align: center;
      font-size: 1.1rem;
      opacity: 0.95;
    }

    .container {
      display: flex;
      flex-direction: column;
      gap: 25px;
      padding: 40px 20px;
      max-width: 1000px;
      margin: auto;
    }

    .card-link {
      text-decoration: none;
      color: inherit;
    }

    .card {
      background: white;
      border-left: 6px solid #2980b9;
      border-radius: 10px;
      padding: 25px 30px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
      background-color: #fdfefe;
    }

    .card h3 {
      margin: 0 0 10px;
      font-size: 1.6rem;
      color: #34495e;
    }

    .card p {
      margin: 0;
      color: #7f8c8d;
      font-size: 0.95rem;
    }

    footer {
      text-align: center;
      padding: 30px 0;
      font-size: 0.9rem;
      color: #999;
    }
  </style>
</head>
<body>

  <header>
    <a href="Cerrar_sesion.php" class="logout-btn">Cerrar sesión</a>
    <h1>Panel de Gestión Administrativa</h1>
    <p>Visualiza y controla todos los módulos del sistema de recompensas</p>
  </header>

  <div class="container">
    <a href="Seccion_Clientes.php" target="_blank" class="card-link">
      <div class="card">
        <h3>Clientes</h3>
        <p>Consulta la lista de clientes registrados y su información general.</p>
      </div>
    </a>
    <a href="Seccion_Beneficios.php" target="_blank" class="card-link">
      <div class="card">
        <h3>Beneficios</h3>
        <p>Administra los beneficios activos disponibles para los clientes.</p>
      </div>
    </a>
    <a href="Seccion_Premios.php" target="_blank" class="card-link">
      <div class="card">
        <h3>Premios</h3>
        <p>Gestiona los premios disponibles para canjear puntos acumulados.</p>
      </div>
    </a>
    <a href="Seccion_Bonificacion.php" target="_blank" class="card-link">
      <div class="card">
        <h3>Bonificaciones</h3>
        <p>Configura las bonificaciones especiales aplicables a los usuarios.</p>
      </div>
    </a>
    <a href="Seccion_CanjePuntos.php" target="_blank" class="card-link">
      <div class="card">
        <h3>Canjes de Puntos</h3>
        <p>Supervisa las transacciones de canje realizadas en la plataforma.</p>
      </div>
    </a>
  </div>

  <footer>
    Plataforma de Recompensas © <?php echo date("Y"); ?>
  </footer>

</body>
</html>
