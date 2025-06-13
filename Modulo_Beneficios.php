<?php
session_start();
// 1) Verificar rol admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: Login.php');
    exit;
}

// Conexión a BD 
$host='localhost'; $db='fidelizacion'; $user='root'; $pass='';
$pdo=new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4",$user,$pass,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
]);

$action = $_GET['action'] ?? 'list';
$uploadDir = __DIR__ . '/img/beneficios/';
if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Borrado
    if ($_POST['form_type']==='delete_beneficio') {
        $stmt = $pdo->prepare("SELECT imagen FROM beneficios WHERE id_beneficio=?");
        $stmt->execute([$_POST['id']]);
        $old = $stmt->fetchColumn();
        if ($old && file_exists(__DIR__.'/'.$old)) unlink(__DIR__.'/'.$old);

        $pdo->prepare("DELETE FROM beneficios WHERE id_beneficio=?")
            ->execute([$_POST['id']]);
        header('Location: Modulo_Beneficios.php');
        exit;
    }

    // Almacenar beneficio
    if ($_POST['form_type']==='save_beneficio') {
        $id            = $_POST['id'] ?: null;
        $nombre_empresa = $_POST['nombre_empresa']; 
$descripcion   = $_POST['descripcion'];
$desde         = $_POST['vigencia_de']; 
$hasta         = $_POST['vigente_hasta']; 
$activo        = isset($_POST['activo']) ? 1 : 0;

        $activo        = isset($_POST['activo'])?1:0;

        // Subida imagen
        $imagenPath = null;
        if (!empty($_FILES['imagen']['name'])) {
            $tmp  = $_FILES['imagen']['tmp_name'];
            $ext  = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $name = uniqid('ben_').'.'.$ext;
            move_uploaded_file($tmp, $uploadDir.$name);
            $imagenPath = 'img/beneficios/'.$name;
        }

        if ($id) {
            // Modificar beneficio
            $sql = "UPDATE beneficios 
                       SET nombre_empresa=?, descripcion=?, descuento=?, vigencia_de=?, vigencia_hasta=?, activo=?"
                  .($imagenPath? ", imagen=?" : "")
                  ." WHERE id_beneficio=?";
            $stmt = $pdo->prepare($sql);
            $params = [$nombre_empresa,$descripcion,$descuento,$desde,$hasta,$activo];
            if ($imagenPath) $params[] = $imagenPath;
            $params[] = $id;
            $stmt->execute($params);
        } else {
            // Dar de alta nuevo beneficio
            $sql = "INSERT INTO beneficios 
                      (nombre_empresa, descripcion, descuento, vigencia_de, vigencia_hasta, activo, imagen)
                    VALUES (?,?,?,?,?,?,?)";
            $pdo->prepare($sql)
                ->execute([$nombre_empresa,$descripcion,$descuento,$desde,$hasta,$activo,$imagenPath]);
        }

        header('Location: Modulo_Beneficios.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Admin – Beneficios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f5f7fa; }
    .img-thumb {
      width: 100px;
      height: 70px;
      object-fit: cover;
      border-radius: 4px;
    }
    .header { padding: 1rem; display: flex; align-items: center; }
    .header a { margin-right: 1rem; font-size: 1.5rem; color: #556; text-decoration: none; }
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
  </style>
</head>
<body>
<div class="container py-4">
  <div class="page-header">
    <a href="Interfaz_administrador.php" class="btn btn-outline-secondary">Regresar</a>
    <h2>Administración de Beneficios</h2>
  </div>

  <?php if ($action==='list'): ?>
    <a href="?action=add" class="btn btn-primary mb-3">Alta Beneficio</a>
    <table class="table table-striped bg-white rounded shadow-sm">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Imagen</th>
          <th>Empresa</th>
          <th>Descuento</th>
          <th>Descripción</th>
          <th>Desde</th>
          <th>Hasta</th>
          <th>Activo</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php
        $stmt = $pdo->query("SELECT * FROM beneficios ORDER BY nombre_empresa");
        while($b = $stmt->fetch()):
      ?>
        <tr>
          <td><?= $b['id_beneficio'] ?></td>
          <td>
            <?php if($b['imagen'] && file_exists(__DIR__.'/'.$b['imagen'])): ?>
              <img src="<?= htmlspecialchars($b['imagen']) ?>" class="img-thumb">
            <?php else: ?>
              — sin imagen —
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($b['nombre_empresa']) ?></td>
          <td><?= htmlspecialchars($b['descuento']) ?></td>
          <td><?= htmlspecialchars($b['descripcion']) ?></td>

          <td><?= $b['vigencia_de'] ?></td>
          <td><?= $b['vigencia_hasta'] ?></td>
          <td><?= $b['activo']? 'Sí':'No' ?></td>
          <td>
            <a href="?action=edit&id=<?= $b['id_beneficio'] ?>" class="btn btn-sm btn-warning">Modificar</a>
            <form method="POST" style="display:inline" onsubmit="return confirm('¿Eliminar?')">
              <input type="hidden" name="form_type" value="delete_beneficio">
              <input type="hidden" name="id" value="<?= $b['id_beneficio'] ?>">
              <button class="btn btn-sm btn-danger">Dar baja</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>

  <?php elseif(in_array($action, ['add','edit'])):
    $id = $_GET['id'] ?? null;
    $b = ['nombre_empresa'=>'','descripcion'=>'','descuento'=>'','vigencia_de'=>'','vigencia_hasta'=>'','activo'=>1,'imagen'=>''];
    if ($id) {
      $stmt = $pdo->prepare("SELECT * FROM beneficios WHERE id_beneficio=?");
      $stmt->execute([$id]);
      $b = $stmt->fetch();
    }
  ?>
    <div class="card p-4 shadow-sm">
      <h4><?= $id? 'Editar':'Nuevo' ?> Beneficio</h4>
      <form method="POST" enctype="multipart/form-data" class="mt-3">
        <input type="hidden" name="form_type" value="save_beneficio">
        <?php if($id):?><input type="hidden" name="id" value="<?= $id ?>"><?php endif;?>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Empresa</label>
            <input type="text" name="empresa" class="form-control" required
       value="<?= htmlspecialchars($b['nombre_empresa']) ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Descuento</label>
            <input type="text" name="descuento" class="form-control"
                   value="<?= htmlspecialchars($b['descuento']) ?>">
          </div>
        </div>

        <div class="mb-3">
  <label class="form-label">Descripción</label>
  <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($b['descripcion']) ?></textarea>
</div>


        <div class="row mb-3">
          <div class="col">
            <label class="form-label">Vigencia De</label>
            <input type="date" name="de" class="form-control"
                   value="<?= $b['vigencia_de'] ?>">
          </div>
          <div class="col">
            <label class="form-label">Vigente Hasta</label>
            <input type="date" name="hasta" class="form-control"
                   value="<?= $b['vigencia_hasta'] ?>">
          </div>
        </div>

        <div class="form-check mb-3">
          <input type="checkbox" name="activo" id="activo" class="form-check-input"
                 <?= $b['activo']? 'checked':'' ?>>
          <label for="activo" class="form-check-label">Activo</label>
        </div>

        <div class="mb-4">
          <label class="form-label">Imagen (100×70px)</label>
          <?php if($b['imagen'] && file_exists(__DIR__.'/'.$b['imagen'])): ?>
            <div class="mb-2">
              <img src="<?= htmlspecialchars($b['imagen']) ?>" class="img-thumb">
            </div>
          <?php endif; ?>
          <input type="file" name="imagen" accept="image/*" class="form-control">
        </div>

        <button class="btn btn-success"><?= $id? 'Actualizar' : 'Guardar' ?></button>
        <a href="Modulo_Beneficios.php" class="btn btn-secondary">Cancelar</a>
      </form>
    </div>
  <?php endif; ?>

</div>
</body>
</html>
