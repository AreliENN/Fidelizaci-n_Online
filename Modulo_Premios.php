<?php
session_start();
// Verificar rol admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: Login.php');
    exit;
}

// Conexión a BD
$pdo = new PDO(
    "mysql:host=localhost;dbname=fidelizacion;charset=utf8mb4",
    "root", "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$action = $_GET['action'] ?? 'list';

// Función auxiliar para procesar subida a carpeta img/
function handleUpload($fieldName, &$error) {
    if (empty($_FILES[$fieldName]['name']) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $tmp  = $_FILES[$fieldName]['tmp_name'];
    $name = basename($_FILES[$fieldName]['name']);
    $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif'];
    if (!in_array($ext, $allowed)) {
        $error = "Formato de imagen no válido. Solo JPG, PNG o GIF.";
        return null;
    }

    // Carpeta img/
    $uploadDir = __DIR__ . '/img/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            $error = "No se pudo crear carpeta img/.";
            return null;
        }
    }

    // Nombre único de premio
    $newName = uniqid('premio_', true) . "." . $ext;
    $dest = $uploadDir . $newName;
    if (!move_uploaded_file($tmp, $dest)) {
        $error = "Error al mover el archivo a $dest.";
        return null;
    }

    return "img/{$newName}";
}

// Dormularios de Premios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = '';
    if ($_POST['form_type'] === 'save_premio') {
        $id    = $_POST['id'] ?? null;
        $nombre= $_POST['nombre'];
        $descr = $_POST['descripcion'];
        $pts   = (int)$_POST['puntos_requeridos'];
        $stk   = (int)$_POST['disponibles'];
        $act   = isset($_POST['activo']) ? 1 : 0;

        // Subida de imagen
        $imgPath = null;
        if (!empty($_FILES['imagen']['name'])) {
            $imgPath = handleUpload('imagen', $error);
            if ($error) {
                $_SESSION['error'] = $error;
                header("Location: Modulo_Premios.php?action=" . ($id ? "edit&id={$id}" : "add"));
                exit;
            }
        }

        if ($id) {
            // Modificar
            if ($imgPath) {
                $stmt = $pdo->prepare("
                  UPDATE premios
                     SET nombre=?, descripcion=?, puntos_requeridos=?, disponibles=?, activo=?, imagen=?
                   WHERE id_premio=?
                ");
                $stmt->execute([$nombre, $descr, $pts, $stk, $act, $imgPath, $id]);
            } else {
                $stmt = $pdo->prepare("
                  UPDATE premios
                     SET nombre=?, descripcion=?, puntos_requeridos=?, disponibles=?, activo=?
                   WHERE id_premio=?
                ");
                $stmt->execute([$nombre, $descr, $pts, $stk, $act, $id]);
            }
        } else {
            // Dar alta Premio
            $stmt = $pdo->prepare("
              INSERT INTO premios (nombre, descripcion, puntos_requeridos, disponibles, activo, imagen)
              VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nombre, $descr, $pts, $stk, $act, $imgPath]);
        }

        header('Location: Modulo_Premios.php');
        exit;
    }

    if ($_POST['form_type'] === 'delete_premio') {
        $id = $_POST['id'];
        $pdo->prepare("DELETE FROM premios WHERE id_premio = ?")
            ->execute([$id]);
        header('Location: Modulo_Premios.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Premios - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">
  <style>
    body { background: #f5f7fa; }
    .header { padding:1rem; display:flex; align-items:center; }
    .header a { margin-right:1rem; font-size:1.5rem; color:#556; text-decoration:none; }
    .card { margin-top:1rem; border-radius:8px; }
    .thumb { width:100px; height:100px; object-fit:cover; border-radius:4px; }
    .form-img { margin-bottom:1rem; }
    .page-header {
      margin: 2rem 0 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 2px solid #007bff;
      padding-bottom: 0.5rem;
    }
    .page-header h2 {
      color: #007bff;
      font-weight: 700;
      margin: 0;
    }
    .btn-outline-secondary {
      min-width: 110px;
    }

    .btn-secondary {
      min-width: 110px;
      font-weight: 600;
      margin-left: 0.7rem;
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
    <h2>Administración de Premios</h2>
  </div>

  <?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
    <?php unset($_SESSION['error']); endif; ?>

  <?php if ($action === 'list'): ?>
    <a href="Modulo_Premios.php?action=add" class="btn btn-primary mb-3">Alta Premio</a>
    <table class="table table-striped card">
      <thead>
        <tr>
          <th>#</th><th>Imagen</th><th>Nombre</th>
          <th>Puntos</th><th>Disponibles</th><th>Activo</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $stmt = $pdo->query("SELECT * FROM premios ORDER BY nombre");
      while($p = $stmt->fetch()):
      ?>
        <tr>
          <td><?= $p['id_premio'] ?></td>
          <td>
            <?php if($p['imagen']): ?>
              <img src="<?= htmlspecialchars($p['imagen']) ?>" class="thumb" alt="">
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($p['nombre']) ?></td>
          <td><?= $p['puntos_requeridos'] ?></td>
          <td><?= $p['disponibles'] ?></td>
          <td><?= $p['activo'] ? 'Sí':'No' ?></td>
          <td>
            <a href="Modulo_Premios.php?action=edit&id=<?= $p['id_premio'] ?>"
               class="btn btn-sm btn-warning">Modificar</a>
            <form method="POST" style="display:inline-block;"
                  onsubmit="return confirm('¿Eliminar este premio?');">
              <input type="hidden" name="form_type" value="delete_premio">
              <input type="hidden" name="id" value="<?= $p['id_premio'] ?>">
              <button class="btn btn-sm btn-danger">Dar Baja</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>

  <?php elseif ($action==='add' || $action==='edit'):
      $id = $_GET['id'] ?? null;
      if ($id) {
          $stmt = $pdo->prepare("SELECT * FROM premios WHERE id_premio = ?");
          $stmt->execute([$id]);
          $p = $stmt->fetch();
      }
  ?>
    <div class="card p-4 mt-4">
      <h4><?= $id ? 'Editar' : 'Nuevo' ?> Premio</h4>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="form_type" value="save_premio">
        <?php if($id): ?>
          <input type="hidden" name="id" value="<?= $id ?>">
        <?php endif; ?>

        <div class="mb-3">
          <label class="form-label">Nombre</label>
          <input type="text" name="nombre" class="form-control"
                 value="<?= $p['nombre'] ?? '' ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control" rows="3"><?= $p['descripcion'] ?? '' ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Puntos Requeridos</label>
          <input type="number" name="puntos_requeridos" class="form-control"
                 value="<?= $p['puntos_requeridos'] ?? 0 ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Disponibles</label>
          <input type="number" name="disponibles" class="form-control"
                 value="<?= $p['disponibles'] ?? 0 ?>" required>
        </div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="activo"
                 name="activo" <?= (!isset($p) || $p['activo']) ? 'checked' : '' ?>>
          <label class="form-check-label" for="activo">Activo</label>
        </div>

        <div class="mb-3 form-img">
          <label class="form-label">Imagen <?= $id ? '(opcional para reemplazar)' : '' ?></label><br>
          <?php if($id && !empty($p['imagen'])): ?>
            <img src="<?= htmlspecialchars($p['imagen']) ?>" class="thumb mb-2" alt=""><br>
          <?php endif; ?>
          <input type="file" name="imagen" accept="image/*" class="form-control">
        </div>

        <button class="btn btn-primary"><?= $id ? 'Actualizar' : 'Guardar' ?></button>
        <a href="Modulo_Premios.php" class="btn btn-secondary">Cancelar</a>
      </form>
    </div>
  <?php endif; ?>

</div>
</body>
</html>
