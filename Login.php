<?php 
session_start();
// Conexión a la base de datos
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

// Paso 2: Confirmación de voz
if (isset($_GET['voice'], $_GET['confirm']) && isset($_SESSION['temp_user_id'])) {
    if ($_GET['confirm'] === $_SESSION['temp_rol']) {
        $_SESSION['user_id'] = $_SESSION['temp_user_id'];
        $_SESSION['nombre']  = $_SESSION['temp_nombre'];
        $_SESSION['rol']     = $_SESSION['temp_rol'];
        unset(
            $_SESSION['temp_user_id'],
            $_SESSION['temp_nombre'],
            $_SESSION['temp_rol'],
            $_SESSION['show_voice']
        );
        header('Location: ' . (
            $_SESSION['rol'] === 'admin'
                ? 'Interfaz_administrador.php'
                : 'Interfaz_cliente.php'
        ));
        exit;
    } else {
        session_destroy();
        header('Location: Login.php?error=voz_incorrecta');
        exit;
    }
}

if (isset($_GET['voice']) && empty($_SESSION['show_voice'])) {
    header('Location: Login.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefono = trim($_POST['telefono']);
    $password = trim($_POST['password']);

    if (empty($telefono) || empty($password)) {
        $error = 'Por favor, completa todos los campos.';
    } else {
        $stmt = $pdo->prepare(
            'SELECT id_admin AS id, nombre, contraseña FROM administrador WHERE telefono = ?'
        );
        $stmt->execute([$telefono]);
        $user = $stmt->fetch();
        $rol  = 'admin';

        if (!$user) {
            $stmt = $pdo->prepare(
                'SELECT id_cliente AS id, nombre, contraseña FROM cliente WHERE telefono_movil = ?'
            );
            $stmt->execute([$telefono]);
            $user = $stmt->fetch();
            $rol  = 'cliente';
        }

        if ($user && $password === $user['contraseña']) {
            $_SESSION['temp_user_id'] = $user['id'];
            $_SESSION['temp_nombre']  = $user['nombre'];
            $_SESSION['temp_rol']     = $rol;
            $_SESSION['show_voice']   = true;
            header('Location: login.php?voice=1');
            exit;
        }

        $error = 'Las credenciales ingresadas no son válidas.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fidelización</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            height: 100vh;
            background: linear-gradient(to right, #f3f4f6, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-form {
            background: #fff;
            padding: 3rem 3.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        .login-form h3 {
            font-size: 2rem;
            margin-bottom: 2rem;
        }
        .form-control {
            margin-bottom: 1.5rem;
            padding: 1rem;
            font-size: 1.1rem;
            border-radius: 10px;
            border: 1px solid #d1d5db;
        }
        .btn-login {
            width: 100%;
            padding: 1rem;
            background-color: #3b82f6;
            color: white;
            transition: background-color 0.3s ease;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
        }
        .btn-login:hover { background-color: #2563eb; }
        .error-alert { color: #c0392b; margin-bottom: 1.5rem; }
        #voiceModal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: 1050;
        }
        #voiceContent {
            background: #ffffff;
            border: 2px solid #3b82f6;
            padding: 2rem;
            border-radius: 16px;
            width: 90%;
            max-width: 400px;
            margin: 6% auto;
            text-align: center;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.3);
            position: relative;
        }
        .microphone-icon {
            font-size: 2rem;
            color: #3b82f6;
            margin-bottom: 0.5rem;
        }
        #voiceSentence {
            font-weight: bold;
            font-size: 1.4rem;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        #voiceInput {
            text-align: center;
            font-style: italic;
            color: #374151;
        }
        #voiceStatus {
            margin-top: 1rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="login-form">
    <h3>Iniciar Sesión</h3>
    <?php if ($error && !isset($_GET['voice'])): ?>
        <div class="error-alert text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!isset($_GET['voice'])): ?>
        <form method="POST">
            <input type="text" name="telefono" class="form-control" placeholder="Teléfono" required pattern="\d{10,15}">
            <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            <button type="submit" class="btn-login">Continuar</button>
        </form>
    <?php else: ?>
        <div id="voiceModal">
            <div id="voiceContent">
                <div class="microphone-icon">🎤</div>
                <h4 class="mb-3">Autenticación por Voz</h4>
                <p class="mb-2">Pronuncia la siguiente palabra clave:</p>
                <p id="voiceSentence">Cargando...</p>
                <input id="voiceInput" class="form-control" readonly placeholder="Texto reconocido">
                <button id="startListening" class="btn-login mt-3">Hablar</button>
                <p id="voiceStatus"></p>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php if (isset($_GET['voice'])): ?>
<script>
    const sentences = ['acceder','validar','continuar','confirmado','bienvenido','ingresar','autenticado','inicio','permitido'];
    let fraseActual = '', validado = false;
    const modal = document.getElementById('voiceModal'), sentenceEl = document.getElementById('voiceSentence');
    const inputEl = document.getElementById('voiceInput'), statusEl = document.getElementById('voiceStatus');
    const btn = document.getElementById('startListening');
    function normalizar(txt) { return txt.toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu,'').replace(/[.,!?;:]/g,'').trim(); }
    function nuevaFrase() { validado=false; statusEl.textContent=''; inputEl.value=''; btn.textContent='Hablar'; fraseActual=sentences[Math.floor(Math.random()*sentences.length)]; sentenceEl.textContent=fraseActual.charAt(0).toUpperCase()+fraseActual.slice(1); }
    function mostrarModal() { modal.style.display='block'; nuevaFrase(); history.replaceState(null,'',window.location.pathname+'?voice=1'); }
    const SpeechRecognition=window.SpeechRecognition||window.webkitSpeechRecognition;
    if (!SpeechRecognition) { alert('Tu navegador no soporta reconocimiento de voz.'); }
    else {
        const recog=new SpeechRecognition(); recog.lang='es-ES'; recog.interimResults=true; recog.maxAlternatives=1;
        recog.addEventListener('result',e=>{ let txt=''; for(let r of e.results) txt+=r[0].transcript; inputEl.value=txt; });
        recog.addEventListener('end',()=>{ const hablado=normalizar(inputEl.value), objetivo=normalizar(fraseActual); if(hablado===objetivo){ validado=true; statusEl.style.color='green'; statusEl.textContent='✅ Autenticación exitosa.'; btn.textContent='Continuar'; } else if(!validado){ statusEl.style.color='red'; statusEl.textContent='❌ No coincidió. Inténtalo nuevamente.'; btn.textContent='Reintentar'; } });
        recog.addEventListener('error',()=>{ statusEl.style.color='red'; statusEl.textContent='⚠️ Error al escuchar. Inténtalo otra vez.'; btn.textContent='Reintentar'; }); recog.addEventListener('speechend',()=>recog.stop());
        btn.addEventListener('click',()=>{ if(validado){ const rol=<?= json_encode($_SESSION['temp_rol'] ?? '') ?>; window.location.href=window.location.pathname+'?voice=1&confirm='+encodeURIComponent(rol); }else{ if(btn.textContent==='Reintentar') nuevaFrase(); btn.textContent='Escuchando...'; recog.start(); }});
        window.onload=mostrarModal;
    }
</script>
<?php endif; ?>
</body>
</html>