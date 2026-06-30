<?php
require_once '../../api/vendor/autoload.php';
$detect = new Mobile_Detect;

// Cabeceras de seguridad
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

session_start();
if (isset($_SESSION["NOMBRE_USUARIO"])) {
    header('Location: iniciarSessionSeleccion.php');
    exit;
}

include_once '../../assest/controlador/USUARIO_ADO.php';
include_once '../../assest/controlador/PTUSUARIO_ADO.php';
include_once "../../assest/controlador/AUSUARIO_ADO.php";
include_once '../../assest/modelo/USUARIO.php';

$USUARIO_ADO = new USUARIO_ADO();
$PTUSUARIO_ADO = new PTUSUARIO_ADO();
$AUSUARIO_ADO = new AUSUARIO_ADO;
$USUARIO = new USUARIO;

// Generar token CSRF si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Helper para escapar salida
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso Exportadora - SmartBerry ONE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../assest/img/favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script src="../../assest/js/sweetalert2@11.js"></script>
    <link rel="stylesheet" href="../../assest/css/fruta-form-compact.css">
    <style>
        :root {
            --azul: #001b5d;
            --azul-2: #082768;
            --rojo: #d81046;
            --verde: #238b3b;
            --texto: #1f2a44;
            --muted: #68758a;
            --muted-2: #8b96a8;
            --border: #e2e8f0;
            --border-2: #d8e1ec;
            --bg: #f6f8fb;
            --white: #ffffff;
            --soft-blue: #f3f6fb;
            --soft-green: #f4faf6;
            --shadow-card: 0 18px 42px rgba(0, 27, 93, 0.08);
            --shadow-soft: 0 10px 24px rgba(0, 27, 93, 0.06);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; min-height: 100%; font-family: 'Montserrat', Arial, sans-serif; background: var(--bg); color: var(--texto); }
        body { overflow-x: hidden; }
        .page { min-height: 100vh; display: grid; grid-template-columns: 460px 1fr; background: var(--bg); }
        .left-panel { background: var(--white); border-right: 1px solid var(--border); display: flex; align-items: center; justify-content: center; padding: 34px 36px; position: relative; z-index: 2; }
        .left-content { width: 100%; max-width: 372px; }
        .brand { margin-bottom: 22px; }
        .brand img { width: 100%; max-width: 280px; height: auto; display: block; margin-bottom: 18px; }
        .module-badge { display: inline-flex; align-items: center; gap: 7px; padding: 7px 11px; border-radius: 999px; background: var(--soft-green); border: 1px solid #dcefe2; color: var(--verde); font-size: 0.66rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 13px; }
        .module-badge .material-icons-round { font-size: 15px; }
        .title { color: var(--azul); font-size: 1.18rem; line-height: 1.35; font-weight: 600; letter-spacing: -0.025em; margin: 0; }
        .subtitle { margin-top: 8px; color: var(--muted); font-size: 0.76rem; line-height: 1.6; font-weight: 400; }
        .site-link { margin-top: 12px; display: inline-flex; align-items: center; gap: 7px; color: var(--azul); font-size: 0.74rem; font-weight: 500; text-decoration: none; }
        .site-link:hover { color: var(--rojo); }
        .site-link .material-icons-round { font-size: 16px; color: var(--rojo); }
        .login-card { background: var(--white); border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow-card); padding: 18px; }
        .card-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 15px; }
        .card-title { color: var(--azul); font-size: 0.74rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; }
        .secure-pill { display: inline-flex; align-items: center; gap: 5px; padding: 6px 9px; border-radius: 999px; background: #f8fafc; border: 1px solid var(--border); color: var(--muted); font-size: 0.65rem; font-weight: 500; white-space: nowrap; }
        .secure-pill .material-icons-round { font-size: 14px; color: var(--verde); }
        .form-grid, .form-group { display: grid; }
        .form-grid { gap: 10px; }
        .form-group { gap: 5px; }
        .form-label { color: var(--azul); font-size: 0.67rem; font-weight: 600; letter-spacing: 0.035em; text-transform: uppercase; }
        .form-input { width: 100%; height: 40px; border: 1px solid var(--border); border-radius: 10px; background: var(--white); color: var(--texto); font-family: inherit; font-size: 0.76rem; font-weight: 400; outline: none; padding: 0 12px; transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease; }
        .form-input::placeholder { color: #a0aabc; }
        .form-input:focus { border-color: #b9d8c2; box-shadow: 0 0 0 3px rgba(35, 139, 59, 0.08); }
        select.form-input { appearance: none; padding-right: 36px; background-image: linear-gradient(45deg, transparent 50%, #9aa6b8 50%), linear-gradient(135deg, #9aa6b8 50%, transparent 50%); background-position: calc(100% - 17px) 17px, calc(100% - 12px) 17px; background-size: 5px 5px; background-repeat: no-repeat; }
        .option-box { margin-top: 2px; padding: 11px 12px; border: 1px solid var(--border); border-radius: 10px; background: #fbfcfd; display: flex; gap: 9px; align-items: flex-start; }
        .option-box input { width: 15px; height: 15px; margin-top: 1px; accent-color: var(--verde); flex: 0 0 auto; }
        .option-box label { color: var(--muted); font-size: 0.69rem; line-height: 1.45; font-weight: 400; cursor: pointer; }
        .security-note { display: flex; align-items: center; justify-content: center; gap: 6px; margin: 3px 0 0; color: var(--verde); font-size: 0.69rem; font-weight: 500; }
        .security-note .material-icons-round { font-size: 16px; }
        .btn-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 4px; }
        .btn-row.single { grid-template-columns: 1fr; }
        .btn { height: 41px; border-radius: 10px; border: 1px solid transparent; font-family: inherit; font-size: 0.76rem; font-weight: 600; text-decoration: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.18s ease; }
        .btn:hover { transform: translateY(-1px); }
        .btn-back { background: var(--white); color: var(--azul); border-color: var(--border); }
        .btn-back:hover { border-color: var(--border-2); background: #f8fafc; box-shadow: var(--shadow-soft); }
        .btn-login { background: var(--azul); color: var(--white); border-color: var(--azul); box-shadow: 0 10px 22px rgba(0, 27, 93, 0.14); }
        .btn-login:hover { background: var(--azul-2); border-color: var(--azul-2); }
        .btn-login:disabled { background: #c8ced8; border-color: #c8ced8; box-shadow: none; cursor: not-allowed; transform: none; }
        .footer-text { margin-top: 16px; text-align: center; color: var(--muted-2); font-size: 0.66rem; line-height: 1.5; font-weight: 400; }
        .login-alert { margin-bottom: 12px; padding: 10px 12px; border: 1px solid #ffe2a3; border-radius: 10px; background: #fff7df; color: #8a5a00; font-size: 0.7rem; font-weight: 600; }
        .right-panel { position: relative; min-height: 100vh; overflow: hidden; background: #0b1f45; }
        .slider-bg, .slide { position: absolute; inset: 0; }
        .slide { background-size: cover; background-position: center; opacity: 0; transition: opacity 1.2s ease; }
        .slide.active { opacity: 1; }
        .image-cover { position: absolute; inset: 0; z-index: 1; background: rgba(0, 27, 93, 0.36); }
        .right-info { position: absolute; left: 42px; bottom: 42px; z-index: 2; width: calc(100% - 84px); max-width: 520px; background: rgba(255,255,255,0.94); border: 1px solid rgba(255,255,255,0.7); border-radius: 16px; padding: 22px; box-shadow: 0 18px 42px rgba(0,0,0,0.14); }
        .right-kicker { display: inline-flex; align-items: center; gap: 7px; padding: 7px 10px; border-radius: 999px; background: var(--soft-blue); border: 1px solid var(--border); color: var(--azul); font-size: 0.66rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 12px; }
        .right-kicker .material-icons-round { font-size: 15px; color: var(--verde); }
        .right-info h2 { color: var(--azul); font-size: 1.08rem; line-height: 1.38; font-weight: 600; letter-spacing: -0.025em; }
        .right-info p { margin-top: 9px; color: #5d697c; font-size: 0.78rem; line-height: 1.6; font-weight: 400; }
        .right-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 15px; }
        .right-tag { display: inline-flex; align-items: center; gap: 6px; padding: 7px 10px; border-radius: 999px; background: #f8fafc; border: 1px solid #e5ebf2; color: var(--azul); font-size: 0.66rem; font-weight: 500; }
        .right-tag .material-icons-round { font-size: 15px; color: var(--verde); }
        @media (max-width: 980px) { .page { grid-template-columns: 1fr; } .left-panel { order: 2; border-right: none; padding: 30px 22px 38px; } .left-content { max-width: 420px; } .right-panel { order: 1; min-height: 300px; } .right-info { left: 22px; bottom: 22px; width: calc(100% - 44px); max-width: none; padding: 18px; } }
        @media (max-width: 560px) { .right-panel { min-height: 235px; } .right-info { display: none; } .brand img { max-width: 252px; } .title { font-size: 1.08rem; } .subtitle { font-size: 0.74rem; } .login-card { padding: 16px; } .card-header { align-items: flex-start; flex-direction: column; gap: 8px; } .btn-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body class="sistemRR">
<main class="page">
    <section class="left-panel">
        <div class="left-content">
            <header class="brand">
                <img src="../../assest/img/logo2.png" alt="SmartBerry ONE">
                <div class="module-badge"><span class="material-icons-round">flight_takeoff</span>Módulo exportadora</div>
                <h1 class="title">Acceso a gestión exportadora SmartBerry ONE</h1>
                <p class="subtitle">Ingresa para administrar procesos exportadores y datos comerciales.</p>
                <a class="site-link" href="https://smartberryone.cl" target="_blank" rel="noopener noreferrer"><span class="material-icons-round">language</span>smartberryone.cl</a>
            </header>

            <section class="login-card">
                <div class="card-header"><h2 class="card-title">Datos de acceso</h2><div class="secure-pill"><span class="material-icons-round">verified_user</span>Seguro</div></div>
                <form method="post" id="loginForm" autocomplete="on">
                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                    <div class="form-grid">
                        <div class="form-group"><label class="form-label" for="NOMBRE">Usuario</label><input type="text" class="form-input" id="NOMBRE" name="NOMBRE" placeholder="Nombre de usuario" value="<?= e($NOMBRE) ?>" autocomplete="username" required></div>
                        <div class="form-group"><label class="form-label" for="CONTRASENA">Contraseña</label><input type="password" class="form-input" id="CONTRASENA" name="CONTRASENA" placeholder="Contraseña" autocomplete="current-password" required minlength="6"></div>
                        <div class="option-box"><input type="checkbox" id="PRECARGA_EXPORTACION_EXPORTADORA"><label for="PRECARGA_EXPORTACION_EXPORTADORA">Cargar información pesada en segundo plano.</label></div>
                        <div class="security-note"><span class="material-icons-round">lock</span>Acceso privado para usuarios autorizados</div>
                        <div class="btn-row"><a href="https://gocreative.cl/smartberry/estadistica/vista/iniciarSession.php" class="btn btn-back">Portal Productores</a><button type="submit" class="btn btn-login" name="ENTRAR">Entrar</button></div>
                    </div>
                </form>
            </section>
            <div class="footer-text">SmartBerry ONE · Portal privado de exportadora.</div>
        </div>
    </section>
    <section class="right-panel">
        <div class="slider-bg"><div class="slide active" style="background-image:url('../../assest/img/abeja.jpg')"></div><div class="slide" style="background-image:url('../../assest/img/arandano.jpg')"></div><div class="slide" style="background-image:url('../../assest/img/esparragos.jpg')"></div></div>
        <div class="image-cover"></div>
        <div class="right-info"><div class="right-kicker"><span class="material-icons-round">flight_takeoff</span>Gestión exportadora</div><h2>Exportación con datos claros y seguros</h2><p>Accede al módulo exportador manteniendo trazabilidad, control y continuidad operacional.</p><div class="right-tags"><div class="right-tag"><span class="material-icons-round">eco</span>Producción</div><div class="right-tag"><span class="material-icons-round">qr_code_scanner</span>Trazabilidad</div><div class="right-tag"><span class="material-icons-round">shield</span>Control</div></div></div>
    </section>
</main>
<script>
    const slides = document.querySelectorAll('.slide');
    let currentSlide = 0;
    setInterval(() => { slides[currentSlide].classList.remove('active'); currentSlide = (currentSlide + 1) % slides.length; slides[currentSlide].classList.add('active'); }, 5000);
    const loginForm = document.getElementById('loginForm');
    if (loginForm) { loginForm.addEventListener('submit', () => { const precarga = document.getElementById('PRECARGA_EXPORTACION_EXPORTADORA')?.checked; if (precarga) { localStorage.setItem('prefetch_despachoex_exportacion', '1'); } else { localStorage.removeItem('prefetch_despachoex_exportacion'); } }); }
</script>
<?php
// ==== LÓGICA LOGIN ====
if (isset($_POST['ENTRAR'])) {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo '<script>Swal.fire({ icon:"error", title:"Error de seguridad", text:"Token CSRF inválido. Recarga la página." });</script>';
        exit;
    }

    $NOMBRE = trim($_POST['NOMBRE'] ?? '');
    $CONTRASENA = $_POST['CONTRASENA'] ?? '';

    if ($NOMBRE === '' || $CONTRASENA === '') {
        echo '<script>Swal.fire({ icon:"warning", title:"Alerta", text:"Debes ingresar usuario y contraseña" });</script>';
        exit;
    }

    if (mb_strlen($CONTRASENA) < 6) {
        echo '<script>Swal.fire({ icon:"warning", title:"Contraseña inválida", text:"Debe tener al menos 6 caracteres." });</script>';
        exit;
    }

    $ARRAYINICIOSESSION = $USUARIO_ADO->iniciarSession($NOMBRE, $CONTRASENA);
    if (!$ARRAYINICIOSESSION) {
        echo '<script>Swal.fire({ icon:"error", title:"Error", text:"Usuario o contraseña incorrectos" });</script>';
        exit;
    }

    $_SESSION["ID_USUARIO"] = $ARRAYINICIOSESSION[0]['ID_USUARIO'];
    $_SESSION["NOMBRE_USUARIO"] = $ARRAYINICIOSESSION[0]['NOMBRE_USUARIO'];
    $_SESSION["TIPO_USUARIO"] = $ARRAYINICIOSESSION[0]['ID_TUSUARIO'];
    session_regenerate_id(true);

    echo '<script>
      Swal.fire({
        icon:"success",
        title:"Éxito",
        text:"Inicio de sesión correcto",
        timer:2000,
        timerProgressBar:true,
        showConfirmButton:false,
        willClose:()=>{ window.location.href="iniciarSessionSeleccion.php"; }
      });
    </script>';
    exit;
}
?>
</body>
</html>
