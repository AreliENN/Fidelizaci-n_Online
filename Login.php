<?php
session_start();
// Configuración de la conexión a la base de datos
$host = 'localhost';
$db   = 'fidelizacion';
$user = 'root';
$pass = ''; 
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $telefono           = trim($_POST['telefono']);
    $password           = trim($_POST['password']);
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verificación de reCAPTCHA
    $secret = '6LcFLMsqAAAAAMMmKCNOan23g4-5xjADqBnfF2-q';
    $verify = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$recaptcha_response}"
    );
    $response_data = json_decode($verify);

    if (empty($telefono) || empty($password)) {
        $error = 'Ingresa teléfono y contraseña.';
    } elseif (!$response_data->success) {
        $error = 'Error de reCAPTCHA. Inténtalo de nuevo.';
    } else {
        // Inicio sesión admin
        $stmt = $pdo->prepare('SELECT id_admin AS id, nombre, contraseña FROM administrador WHERE telefono = ?');
        $stmt->execute([$telefono]);
        $admin = $stmt->fetch();
        if ($admin && $password === $admin['contraseña']) {
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['nombre']  = $admin['nombre'];
            $_SESSION['rol']     = 'admin';
            echo "<script>
                    alert('Bienvenido, {$admin['nombre']}');
                    window.location.href='Interfaz_administrador.php';
                  </script>";
            exit;
        }

        // Inicio sesión cliente
        $stmt = $pdo->prepare('SELECT id_cliente AS id, nombre, contraseña FROM cliente WHERE telefono_movil = ?');
        $stmt->execute([$telefono]);
        $cliente = $stmt->fetch();
        if ($cliente && $password === $cliente['contraseña']) {
            $_SESSION['user_id'] = $cliente['id'];
            $_SESSION['nombre']  = $cliente['nombre'];
            $_SESSION['rol']     = 'cliente';
            echo "<script>
                    alert('Bienvenido, {$cliente['nombre']}');
                    window.location.href='Interfaz_cliente.php';
                  </script>";
            exit;
        }
        $error = 'Teléfono o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            height: 100vh;
            background: linear-gradient(135deg, #4b6cb7, #182848);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            background: #fff;
            padding: 40px 30px;
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            z-index: 10;
        }

        .login-box h3 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
            font-weight: 600;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px;
            font-size: 16px;
        }

        .btn-primary {
            background-color: #4b6cb7;
            border-color: #4b6cb7;
            padding: 10px;
            font-weight: 600;
            font-size: 16px;
            border-radius: 10px;
        }

        .btn-primary:hover {
            background-color: #3b5998;
        }

        .error-alert {
            background-color: #f8d7da;
            color: #842029;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #f5c2c7;
            text-align: center;
        }

        .form-label {
            margin-top: 10px;
            font-weight: 500;
            color: #555;
        }

        .g-recaptcha {
            margin-top: 15px;
            margin-bottom: 15px;
        }
    </style>
    <script>
        function validarFormulario(event) {
            var response = grecaptcha.getResponse();
            if (response.length === 0) {
                event.preventDefault();
                alert("Por favor, completa el reCAPTCHA antes de continuar.");
            }
        }
    </script>
</head>
<body>
    <div class="login-box">
        <h3>Iniciar sesión</h3>
        <?php if (!empty($error)): ?>
            <div class="error-alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" onsubmit="validarFormulario(event)">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control" pattern="\d{10,15}" title="Sólo números, entre 10 y 15 dígitos" required>

            <label class="form-label">Contraseña</label>
            <input type="password" name="password" class="form-control" required>

            <div class="g-recaptcha" data-sitekey="6LcFLMsqAAAAAO5WlI_bGH3Dyd-Isf_4Raoh9QPP"></div>

            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
    </div>
</body>
</html>
