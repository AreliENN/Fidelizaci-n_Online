<?php 
session_start();

// Verificar rol de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: Login.php');
    exit;
}

// Conexión a la base de datos
$host = 'localhost';
$db   = 'fidelizacion';
$user = 'root';
$pass = '';
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$pdo  = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$action = $_GET['action'] ?? 'list';
$error  = '';

handleFormSubmission($pdo);

function handleFormSubmission($pdo) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($_POST['form_type'] === 'register_purchase') {
            $id_cliente = $_POST['id_cliente'];
            $monto = (float)$_POST['monto_compra'];
            $pts = floor($monto / 100) * 5;

            $stmt = $pdo->prepare("INSERT INTO bonificacion_puntos(id_cliente, monto_compra, puntos_acreditados) VALUES (?, ?, ?)");
            $stmt->execute([$id_cliente, $monto, $pts]);

            $pdo->prepare("UPDATE cliente SET puntos = puntos + ? WHERE id_cliente = ?")
                ->execute([$pts, $id_cliente]);

            header('Location: Modulo_Puntos.php');
            exit;
        }

        if ($_POST['form_type'] === 'redeem_points') {
            $id_cliente = $_POST['id_cliente'];
            $id_premio  = $_POST['id_premio'];
            $pts_req    = (int)$_POST['puntos_usados'];

            $stmt = $pdo->prepare("SELECT puntos FROM cliente WHERE id_cliente = ?");
            $stmt->execute([$id_cliente]);
            $current = (int)$stmt->fetchColumn();
            $remaining = $current - $pts_req;

            $stmt = $pdo->prepare("
                INSERT INTO canje_puntos (id_cliente, id_premio, puntos_usados, puntos_restantes)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$id_cliente, $id_premio, $pts_req, $remaining]);

            $pdo->prepare("UPDATE cliente SET puntos = ? WHERE id_cliente = ?")
                ->execute([$remaining, $id_cliente]);

            header('Location: Modulo_Puntos.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Puntos - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/general.css" />
    <style>
        body {
  font-family: 'Segoe UI', 'Helvetica Neue', sans-serif;
  font-size: 16px;
  line-height: 1.6;
  color: #333;
}

        .header { padding: 1rem; display: flex; align-items: center; }
        .header a { margin-right: 1rem; font-size: 1.5rem; color: #556; text-decoration: none; }
        .card {
  padding: 1.5rem;
  box-shadow: 0 0 8px rgba(0,0,0,0.05); /* sombra ligera */
  border: 1px solid #e0e0e0;
}
.btn {
  padding: 0.5rem 1.2rem;
  border-radius: 6px;
  font-weight: 500;
}


        .thumb { width: 100px; height: 100px; object-fit: cover; border-radius: 4px; }
        .form-img { margin-bottom: 1rem; }
        .page-header {
            margin: 2rem 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
        }
        .page-header h2 { color: #007bff; font-weight: 700; margin: 0; }
        .btn-outline-secondary, .btn-secondary {
            min-width: 110px;
            font-weight: 600;
            margin-left: 0.7rem;
        }
        .table th, .table td {
  vertical-align: middle;
  padding: 0.75rem;
}
.table thead {
  background-color: #f0f2f5;
  font-weight: bold;
}
.table-striped tbody tr:nth-of-type(odd) {
  background-color: #fcfcfc;
}

    </style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <a href="Interfaz_administrador.php" class="btn btn-outline-secondary">Regresar</a>
        <h2>Alta de Puntos</h2>
    </div>

    <?php if ($action === 'list'): ?>
        <a href="Modulo_Puntos.php?action=add" class="btn btn-primary">Registrar Compra</a>
        <h4 class="mt-4">Bonificación de Puntos</h4>
        <table class="table table-striped card">
            <thead>
                <tr>
                    <th>#</th><th>Cliente</th><th>Monto (MXN)</th>
                    <th>Puntos</th><th>Fecha</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $stmt = $pdo->query("
                SELECT t.id_bonificacion,
                       CONCAT(c.nombre,' ',c.apellidos) AS cliente,
                       t.monto_compra,
                       t.puntos_acreditados,
                       t.fecha
                FROM bonificacion_puntos t
                JOIN cliente c ON t.id_cliente = c.id_cliente
                ORDER BY t.fecha DESC
            ");
            while ($t = $stmt->fetch()): ?>
                <tr>
                    <td><?= $t['id_bonificacion'] ?></td>
                    <td><?= htmlspecialchars($t['cliente']) ?></td>
                    <td><?= number_format($t['monto_compra'], 2) ?></td>
                    <td><?= $t['puntos_acreditados'] ?></td>
                    <td><?= $t['fecha'] ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

    <?php elseif ($action === 'add'): ?>
        <div class="card p-4 mt-4">
            <h4>Registrar Compra</h4>
            <form method="POST">
                <input type="hidden" name="form_type" value="register_purchase">
                <div class="mb-3">
                    <label class="form-label">Cliente</label>
                    <select name="id_cliente" class="form-select" required>
                        <option value="">Selecciona...</option>
                        <?php
                        $stmt = $pdo->query("SELECT id_cliente, nombre, apellidos FROM cliente");
                        while ($c = $stmt->fetch()): ?>
                            <option value="<?= $c['id_cliente'] ?>">
                                <?= htmlspecialchars($c['nombre'] . ' ' . $c['apellidos']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Monto de Compra (MXN)</label>
                    <input type="number" step="0.01" name="monto_compra" id="monto_compra" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Puntos a Acreditar</label>
                    <input type="number" name="puntos_acreditados" id="puntos_acreditados" class="form-control" readonly>
                </div>
                <button class="btn btn-primary">Registrar</button>
                <a href="Modulo_Puntos.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>

        <script>
            document.getElementById('monto_compra').addEventListener('input', function () {
                const v = parseFloat(this.value) || 0;
                document.getElementById('puntos_acreditados').value = Math.floor(v / 100) * 5;
            });
        </script>

        <script>
            const clienteSel = document.getElementById('cliente_sel');
            const premioSel = document.getElementById('premio_sel');
            const puntosUs = document.getElementById('puntos_usados');
            const puntosRest = document.getElementById('puntos_restantes');
            const btnRedeem = document.getElementById('btnRedeem');

            function updateRedeem() {
                const cl = parseInt(clienteSel.selectedOptions[0]?.dataset.puntos || 0);
                const pr = parseInt(premioSel.selectedOptions[0]?.dataset.ptsreq || 0);
                const ok = cl >= pr;
                puntosUs.value = ok ? pr : 0;
                puntosRest.value = ok ? (cl - pr) : cl;
                btnRedeem.disabled = !ok;
            }

            clienteSel.onchange = updateRedeem;
            premioSel.onchange = updateRedeem;
        </script>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
