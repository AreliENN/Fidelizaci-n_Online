<?php
session_start();
ini_set('display_errors',1);
error_reporting(E_ALL);

// Verificar rol admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: Login.php');
    exit;
}

// Conexión a BD
$dsn = "mysql:host=localhost;dbname=fidelizacion;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Error BD: " . $e->getMessage());
}

$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Obtener metadatos de columnas
$cols = [];
$stmt = $pdo->query("DESCRIBE cliente");
while ($col = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $cols[] = $col;
}

// Columna PK y lista de campos editables
$pk = null;
$fields = [];
foreach ($cols as $col) {
    if ($col['Key'] === 'PRI') {
        $pk = $col['Field'];
        continue;
    }
    // Omitimos campos automáticos si los hay
    if (in_array($col['Field'], ['creado_en'])) {
        continue;
    }
    $fields[] = $col;
}

// Procesar POST 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];
    foreach ($fields as $col) {
        $name = $col['Field'];
        $data[$name] = $_POST[$name] ?? null;
    }

    if ($_POST['form_type'] === 'save') {
        if (!empty($_POST[$pk])) {
            // Modificar cliente
            $sets = [];
            foreach ($fields as $col) {
                $sets[] = $col['Field'] . " = ?";
            }
            $sql = "UPDATE cliente SET " . implode(', ', $sets) . " WHERE $pk = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_merge(array_values($data), [$_POST[$pk]]));
        } else {
            // Agregar cliente
            $names = array_map(fn($c) => $c['Field'], $fields);
            $ph    = array_fill(0, count($names), '?');
            $sql   = "INSERT INTO cliente (" . implode(', ', $names) . ") VALUES (" . implode(', ', $ph) . ")";
            $stmt  = $pdo->prepare($sql);
            $stmt->execute(array_values($data));
        }
    }

    if ($_POST['form_type'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM cliente WHERE $pk = ?");
        $stmt->execute([$_POST[$pk]]);
    }

    header('Location: Modulo_Cliente.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>CRUD Clientes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
    body {
      background-color: #f5f7fa;
    }
    /* Encabezado de la página */
.page-header {
  margin: 2rem 0 1rem;
  padding-bottom: 1rem;
  border-bottom: 3px solid #0d6efd;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

    .page-header h2 {
      color: #007bff;
      font-weight: 700;
      margin: 0;
    }
    .btn-outline-secondary {
      min-width: 110px;
    }
    .card {
      border-radius: 0.75rem;
      box-shadow: 0 4px 12px rgb(0 0 0 / 0.05);
      margin-bottom: 2rem;
      padding: 1.5rem;
      background-color: #ffffff;
    }
    .table-container {
      overflow-x: auto;
    }
    table.table-striped > tbody > tr:nth-of-type(odd) {
      background-color: #f9fbfd;
    }
    table.table-striped > tbody > tr:hover {
      background-color: #dbefff;
    }
    th {
      color: #0056b3;
      font-weight: 600;
    }
    td, th {
      vertical-align: middle !important;
    }
    .btn-sm {
      min-width: 95px;
      font-weight: 600;
    }
    form.d-inline {
      display: inline-block;
      margin-left: 0.3rem;
    }
    h4 {
      color: #0056b3;
      margin-bottom: 1.5rem;
      font-weight: 700;
    }
    .form-label {
      font-weight: 600;
      color: #33475b;
    }
    .mb-3 > input.form-control {
      box-shadow: inset 0 1px 3px rgb(0 0 0 / 0.1);
      border-radius: 0.4rem;
      border: 1px solid #ced4da;
      transition: border-color 0.3s ease;
    }
    .mb-3 > input.form-control:focus {
      border-color: #007bff;
      box-shadow: 0 0 8px rgb(0 123 255 / 0.25);
      outline: none;
    }
    .btn-primary {
      min-width: 110px;
      font-weight: 700;
    }
    .btn-secondary {
      min-width: 110px;
      font-weight: 600;
      margin-left: 0.7rem;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="page-header">
      
      <a href="Interfaz_administrador.php" class="btn btn-outline-secondary">Regresar</a>
      <h2>Clientes</h2>
    </div>

    <?php if ($action === 'list'): ?>
      <!-- Botones con íconos -->
<a href="?action=add" class="btn btn-success mb-3">
  <i class="bi bi-person-plus-fill"></i> Alta Cliente
</a>

      <div class="card">
        <div class="table-container">
          <table class="table table-striped table-hover align-middle">
            <thead>
              <tr>
                <?php foreach ($cols as $col): ?>
                  <th><?= htmlspecialchars($col['Field']) ?></th>
                <?php endforeach ?>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
            <?php
            $rows = $pdo->query("SELECT * FROM cliente ORDER BY $pk")
                        ->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row): ?>
              <tr>
                <?php foreach ($cols as $col): ?>
                  <td><?= htmlspecialchars($row[$col['Field']]) ?></td>
                <?php endforeach ?>
                <td class="text-center">
  <a href="?action=edit&id=<?= $row[$pk] ?>" class="btn btn-sm btn-outline-warning">
    <i class="bi bi-pencil-square"></i> Modificar
  </a>
  <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
    <input type="hidden" name="form_type" value="delete">
    <input type="hidden" name="<?= $pk ?>" value="<?= $row[$pk] ?>">
    <button class="btn btn-sm btn-outline-danger">
      <i class="bi bi-trash3-fill"></i> Dar Baja
    </button>
  </form>
</td>

              </tr>
            <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>

    <?php else:
      $record = [];
      if ($action === 'edit' && $id) {
          $stmt = $pdo->prepare("SELECT * FROM cliente WHERE $pk = ?");
          $stmt->execute([$id]);
          $record = $stmt->fetch(PDO::FETCH_ASSOC);
      }
    ?>
      <div class="card">
        <h4><?= $action === 'add' ? 'Nuevo' : 'Editar' ?> Cliente</h4>
        <form method="POST" novalidate>
          <input type="hidden" name="form_type" value="save">
          <?php if ($action === 'edit'): ?>
            <input type="hidden" name="<?= $pk ?>" value="<?= htmlspecialchars($record[$pk]) ?>">
          <?php endif ?>

          <?php foreach ($fields as $col): 
              $name = $col['Field'];
              $val  = $record[$name] ?? '';
          ?>
            <div class="mb-3">
              <label class="form-label" for="<?= $name ?>"><?= ucfirst($name) ?></label>
              <input
                type="<?= $name === 'correo' ? 'email' : 'text' ?>"
                name="<?= $name ?>"
                id="<?= $name ?>"
                class="form-control"
                value="<?= htmlspecialchars($val) ?>"
                <?= $col['Null'] === 'NO' ? 'required' : '' ?>
              >
            </div>
          <?php endforeach ?>

          <button type="submit" class="btn btn-primary"><?= $action === 'add' ? 'Guardar' : 'Actualizar' ?></button>
          <a href="Modulo_Cliente.php" class="btn btn-secondary">Cancelar</a>
        </form>
      </div>
    <?php endif ?>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

