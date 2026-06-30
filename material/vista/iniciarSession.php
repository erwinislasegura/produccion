<?php
require_once '../../api/vendor/autoload.php';
$detect = new Mobile_Detect;
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

$NOMBRE = "";
$CONTRASENA = "";

// Generar token CSRF si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Inicializar contador de intentos
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Acceso Intranet - SmartBerry One</title>
  <link rel="icon" href="../../assest/img/favicon.png" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
  <script src="../../assest/js/sweetalert2@11.js"></script>
  <style>
    :root{--vf-berry:#7f1734;--vf-berry-dark:#4d0d20;--vf-leaf:#4f8a2f;--vf-leaf-soft:#eaf4e4;--vf-ink:#263238;--vf-muted:#667085;--vf-border:#e6e9ef;--vf-card:rgba(255,255,255,.92)}
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif}
    body,html{min-height:100%;background:#f7f8f5;color:var(--vf-ink)}
    body{background:radial-gradient(circle at top left,rgba(127,23,52,.16),transparent 32%),linear-gradient(135deg,#fff 0%,#f5f8ef 100%)}
    .container{display:flex;min-height:100vh;position:relative;overflow:hidden}
    .left-panel{width:min(100%,460px);background:var(--vf-card);backdrop-filter:blur(18px);padding:48px 42px;display:flex;flex-direction:column;justify-content:center;box-shadow:24px 0 70px rgba(77,13,32,.10);z-index:2}
    .logo{text-align:center;margin-bottom:28px}.logo img{max-width:220px;width:72%;height:auto}.logo p{margin-top:12px;color:var(--vf-muted);font-size:13px}.logo p a{color:var(--vf-berry);text-decoration:none;font-weight:600}.logo p a:hover{text-decoration:underline}
    h2{text-align:center;margin-bottom:8px;color:var(--vf-berry-dark);font-weight:800;letter-spacing:.08em;font-size:20px;text-transform:uppercase}.subtitle{text-align:center;color:var(--vf-muted);font-size:14px;margin-bottom:24px;line-height:1.5}
    .card{border:1px solid rgba(127,23,52,.11);border-radius:24px;padding:24px;background:#fff;box-shadow:0 22px 50px rgba(77,13,32,.10);margin-bottom:18px}.card h3{font-size:17px;margin-bottom:18px;color:var(--vf-berry-dark);display:flex;align-items:center;gap:8px}.card h3:before{content:"";width:10px;height:10px;border-radius:50%;background:var(--vf-leaf);box-shadow:0 0 0 6px var(--vf-leaf-soft)}
    .form-input{width:100%;padding:14px 15px;border:1px solid var(--vf-border);border-radius:14px;margin-bottom:14px;font-size:14px;background:#fbfcfb;transition:border-color .2s,box-shadow .2s,background .2s}.form-input:focus{outline:none;border-color:var(--vf-berry);box-shadow:0 0 0 4px rgba(127,23,52,.12);background:#fff}
    .prefetch-option{display:flex;align-items:flex-start;gap:10px;font-size:12px;color:var(--vf-muted);line-height:1.4;margin:2px 0 14px}.prefetch-option input{accent-color:var(--vf-berry);margin-top:2px}.security-note{display:flex;align-items:center;justify-content:center;gap:7px;margin:12px 0 18px;color:var(--vf-leaf);font-weight:700;font-size:13px}.security-note .material-icons{font-size:19px}
    .btn{width:100%;padding:14px;border:none;border-radius:14px;font-weight:800;cursor:pointer;text-decoration:none;display:block;text-align:center;transition:transform .2s,box-shadow .2s,background .2s}.btn:hover{transform:translateY(-1px)}.btn-login{background:linear-gradient(135deg,var(--vf-berry),#a52749);color:#fff;box-shadow:0 14px 28px rgba(127,23,52,.24)}.btn-login:hover{background:linear-gradient(135deg,var(--vf-berry-dark),var(--vf-berry))}.btn-login:disabled{background:#c7c7c7;box-shadow:none;cursor:not-allowed}.btn-link{color:var(--vf-berry-dark);border:1px solid rgba(127,23,52,.18);background:#fff;margin-top:4px}.btn-link:hover{box-shadow:0 12px 26px rgba(77,13,32,.10)}
    .login-alert{color:#8a5a00;background:#fff7df;border:1px solid #ffe2a3;padding:12px 14px;border-radius:14px;margin-bottom:16px;font-size:13px;font-weight:600}.right-panel{flex:1;position:relative;overflow:hidden;background:#1f2a1d}.right-panel:after{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(77,13,32,.28),rgba(22,44,19,.12)),radial-gradient(circle at 78% 18%,rgba(255,255,255,.20),transparent 28%);z-index:1}.slide{position:absolute;inset:0;background-size:cover;background-position:center;opacity:0;transform:scale(1.04);transition:opacity 1.5s,transform 6s}.slide.active{opacity:1;transform:scale(1)}
    @media(max-width:820px){.container{flex-direction:column}.left-panel{width:100%;padding:34px 22px}.right-panel{min-height:280px;order:-1}.logo img{max-width:190px}}
  </style>
  <link rel="stylesheet" href="../../assest/css/fruta-form-compact.css">

</head>
<body class="sistemRR">
  <div class="container">
    <div class="left-panel">
      <div class="logo">
        <img src="../../assest/img/logo2.png" alt="SmartBerry One" />
        <p><a href="https://smartberryone.cl" target="_blank" rel="noopener noreferrer">smartberryone.cl</a></p>
      </div>
      <h2>SELECCIÓN INTRANET</h2>
      <p class="subtitle">Accede al módulo de gestión con una experiencia segura, limpia y alineada a SmartBerry One.</p>

      <?php if ($_SESSION['login_attempts'] > 0): ?>
      <div class="login-alert">
        ⚠️ Intentos fallidos: <?php echo $_SESSION['login_attempts']; ?>/5
      </div>
      <?php endif; ?>

      <div class="card">
        <h3>Acceso Interno</h3>
        <form method="post" id="loginForm">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>" />
          <input type="text" class="form-input" placeholder="Usuario" name="NOMBRE" value="<?php echo htmlspecialchars($NOMBRE); ?>" required />
          <input type="password" class="form-input" placeholder="Contraseña" name="CONTRASENA" required minlength="6" />
          <div class="prefetch-option">
            <input type="checkbox" id="PRECARGA_EXPORTACION_MATERIAL" style="margin-top:0;">
            <label for="PRECARGA_EXPORTACION_MATERIAL" style="margin-bottom:0;">Cargar información pesada en segundo plano</label>
          </div>
          <div class="security-note">
            <span class="material-icons">lock</span>
            Conexión segura por SSL
          </div>
          <button type="submit" name="ENTRAR" class="btn btn-login">Entrar</button>
        </form>
      </div>
      <a href="https://gocreative.cl/smartberry/estadistica/vista/iniciarSession.php" class="btn-link">Portal Productores</a>
    </div>
    <div class="right-panel">
      <div class="slide active" style="background-image:url('../../assest/img/abeja.jpg')"></div>
      <div class="slide" style="background-image:url('../../assest/img/arandano.jpg')"></div>
      <div class="slide" style="background-image:url('../../assest/img/esparragos.jpg')"></div>
    </div>
  </div>

  <script>
    const slides = document.querySelectorAll('.slide');
    let idx = 0;
    setInterval(() => {
      slides[idx].classList.remove('active');
      idx = (idx + 1) % slides.length;
      slides[idx].classList.add('active');
    }, 5000);

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
      loginForm.addEventListener('submit', () => {
        const precarga = document.getElementById('PRECARGA_EXPORTACION_MATERIAL')?.checked;
        if (precarga) {
          localStorage.setItem('prefetch_despachoex_exportacion', '1');
        } else {
          localStorage.removeItem('prefetch_despachoex_exportacion');
        }
      });
    }
  </script>

<?php
if (isset($_POST['ENTRAR'])) {
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo '<script>
          Swal.fire({
            icon:"error",
            title:"Error de seguridad",
            text:"Token CSRF inválido. Recarga la página."
          });
        </script>';
        exit;
    }

    // Limitar intentos de login
    if ($_SESSION['login_attempts'] >= 5) {
        echo '<script>
          Swal.fire({
            icon:"error",
            title:"Demasiados intentos",
            text:"Has superado el número máximo de intentos. Intenta más tarde."
          });
        </script>';
        exit;
    }

    // Sanitizar entradas
    $NOMBRE = trim($_POST['NOMBRE']);
    $CONTRASENA = $_POST['CONTRASENA'];

    if ($NOMBRE === "" || $CONTRASENA === "") {
        echo '<script>
          Swal.fire({
            icon:"warning",
            title:"Alerta",
            text:"Debes ingresar usuario y contraseña"
          });
        </script>';
        exit;
    }

    if (strlen($CONTRASENA) < 6) {
        echo '<script>
          Swal.fire({
            icon:"warning",
            title:"Alerta",
            text:"La contraseña debe tener al menos 6 caracteres."
          });
        </script>';
        exit;
    }

    $ARRAYINICIOSESSION = $USUARIO_ADO->iniciarSession($NOMBRE, $CONTRASENA);

    if (!$ARRAYINICIOSESSION) {
        $_SESSION['login_attempts']++;
        echo '<script>
          Swal.fire({
            icon:"error",
            title:"Error",
            text:"Usuario o contraseña incorrectos"
          });
        </script>';
        exit;
    } else {
        session_regenerate_id(true);
        $_SESSION['login_attempts'] = 0;

        $_SESSION["ID_USUARIO"] = $ARRAYINICIOSESSION[0]['ID_USUARIO'];
        $_SESSION["NOMBRE_USUARIO"] = $ARRAYINICIOSESSION[0]['NOMBRE_USUARIO'];
        $_SESSION["TIPO_USUARIO"] = $ARRAYINICIOSESSION[0]['ID_TUSUARIO'];

        echo '<script>
          Swal.fire({
            icon:"success",
            title:"Éxito",
            text:"Inicio de sesión correcto",
            timer:2000,
            timerProgressBar:true,
            showConfirmButton:false,
            willClose: () => { window.location.href = "iniciarSessionSeleccion.php"; }
          });
        </script>';
        exit;
    }
}
?>
</body>
</html>
