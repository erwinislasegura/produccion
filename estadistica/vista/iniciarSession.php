<?php
require_once '../../api/vendor/autoload.php';
$detect = new Mobile_Detect;

session_start();
if (isset($_SESSION["NOMBRE_USUARIO"], $_SESSION["ID_TEMPORADA"], $_SESSION["ID_ESPECIE"])) {
    if ($_SESSION["NOMBRE_USUARIO"] && $_SESSION["ID_TEMPORADA"] && $_SESSION["ID_ESPECIE"]) {
        header('Location: index.php');
    }
}

include_once '../../assest/controlador/USUARIO_ADO.php';
include_once '../../assest/controlador/TEMPORADA_ADO.php';
include_once '../../assest/controlador/PTUSUARIO_ADO.php';
include_once "../../assest/controlador/AUSUARIO_ADO.php";
include_once '../../assest/controlador/EMPRESA_ADO.php';
include_once '../../assest/modelo/USUARIO.php';

$USUARIO_ADO = new USUARIO_ADO();
$TEMPORADA_ADO = new TEMPORADA_ADO();
$PTUSUARIO_ADO = new PTUSUARIO_ADO();
$AUSUARIO_ADO = new AUSUARIO_ADO;
$EMPRESA_ADO = new EMPRESA_ADO();
$USUARIO = new USUARIO;

$NOMBRE = "";
$CONTRASENA = "";
$EMPRESA = $_SESSION["ID_EMPRESA"] ?? "";
$TEMPORADA = $_SESSION["ID_TEMPORADA"] ?? "";
$ARRAYEMPRESA = $EMPRESA_ADO->listarEmpresaCBX();
$ARRAYTEMPORADA = $TEMPORADA_ADO->listarTemporadaCBX();

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Productores - SmartBerry One</title>
  <link rel="icon" href="../../assest/img/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <script src="../../assest/js/sweetalert2@11.js"></script>
  <style>
    :root{--vf-berry:#7f1734;--vf-berry-dark:#4d0d20;--vf-leaf:#4f8a2f;--vf-leaf-soft:#eaf4e4;--vf-ink:#263238;--vf-muted:#667085;--vf-border:#e6e9ef;--vf-card:rgba(255,255,255,.92);--vf-page-bg:url('../../assest/img/fondo.jpg')}
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif}
    body,html{min-height:100%;background:#f7f8f5;color:var(--vf-ink)}
    body{background-image:radial-gradient(circle at top left,rgba(127,23,52,.16),transparent 32%),linear-gradient(135deg,rgba(255,255,255,.92) 0%,rgba(245,248,239,.88) 100%),var(--vf-page-bg);background-size:auto,auto,cover;background-position:left top,center,center;background-repeat:no-repeat,no-repeat,no-repeat;background-attachment:fixed,fixed,fixed}
    .container{display:flex;min-height:100vh;position:relative;overflow:hidden}
    .left-panel{width:min(100%,460px);background:var(--vf-card);backdrop-filter:blur(18px);padding:48px 42px;display:flex;flex-direction:column;justify-content:center;box-shadow:24px 0 70px rgba(77,13,32,.10);z-index:2}
    .logo{text-align:center;margin-bottom:28px}.logo img{max-width:220px;width:72%;height:auto}.logo p{margin-top:12px;color:var(--vf-muted);font-size:13px}.logo p a{color:var(--vf-berry);text-decoration:none;font-weight:600}.logo p a:hover{text-decoration:underline}
    h2{text-align:center;margin-bottom:8px;color:var(--vf-berry-dark);font-weight:800;letter-spacing:.08em;font-size:20px;text-transform:uppercase}.subtitle{text-align:center;color:var(--vf-muted);font-size:14px;margin-bottom:24px;line-height:1.5}
    .card{border:1px solid rgba(127,23,52,.11);border-radius:24px;padding:24px;background:#fff;box-shadow:0 22px 50px rgba(77,13,32,.10);margin-bottom:18px}.card h3{font-size:17px;margin-bottom:18px;color:var(--vf-berry-dark);display:flex;align-items:center;gap:8px}.card h3:before{content:"";width:10px;height:10px;border-radius:50%;background:var(--vf-leaf);box-shadow:0 0 0 6px var(--vf-leaf-soft)}
    label{font-size:13px;color:var(--vf-berry-dark);margin-bottom:7px;display:block;font-weight:800}.form-input{width:100%;padding:14px 15px;border:1px solid var(--vf-border);border-radius:14px;margin-bottom:14px;font-size:14px;background:#fbfcfb;transition:border-color .2s,box-shadow .2s,background .2s}.form-input:focus{outline:none;border-color:var(--vf-berry);box-shadow:0 0 0 4px rgba(127,23,52,.12);background:#fff}
    .prefetch-option{display:flex;align-items:flex-start;gap:10px;font-size:12px;color:var(--vf-muted);line-height:1.4;margin:2px 0 14px}.prefetch-option input{accent-color:var(--vf-berry);margin-top:2px}.security-note{display:flex;align-items:center;justify-content:center;gap:7px;margin:12px 0 18px;color:var(--vf-leaf);font-weight:700;font-size:13px}.security-note .material-icons{font-size:19px}
    .btn{width:100%;padding:14px;border:none;border-radius:14px;font-weight:800;cursor:pointer;text-decoration:none;display:block;text-align:center;transition:transform .2s,box-shadow .2s,background .2s}.btn:hover{transform:translateY(-1px)}.btn-login{background:linear-gradient(135deg,var(--vf-berry),#a52749);color:#fff;box-shadow:0 14px 28px rgba(127,23,52,.24)}.btn-login:hover{background:linear-gradient(135deg,var(--vf-berry-dark),var(--vf-berry))}.btn-login:disabled{background:#c7c7c7;box-shadow:none;cursor:not-allowed}.btn-link,.btn-back{color:var(--vf-berry-dark);border:1px solid rgba(127,23,52,.18);background:#fff;margin-top:4px}.btn-link:hover,.btn-back:hover{box-shadow:0 12px 26px rgba(77,13,32,.10)}.btn-group{display:flex;gap:10px}.btn-group .btn{flex:1}
    .login-alert{color:#8a5a00;background:#fff7df;border:1px solid #ffe2a3;padding:12px 14px;border-radius:14px;margin-bottom:16px;font-size:13px;font-weight:600}.right-panel{flex:1;position:relative;overflow:hidden;background:#1f2a1d}.right-panel:after{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(77,13,32,.28),rgba(22,44,19,.12)),radial-gradient(circle at 78% 18%,rgba(255,255,255,.20),transparent 28%);z-index:1}.slide{position:absolute;inset:0;background-size:cover;background-position:center;opacity:0;transform:scale(1.04);transition:opacity 1.5s,transform 6s}.slide.active{opacity:1;transform:scale(1)}
    @media(max-width:820px){.container{flex-direction:column}.left-panel{width:100%;padding:34px 22px}.right-panel{min-height:280px;order:-1}.logo img{max-width:190px}.btn-group{flex-direction:column}}
  </style>
  <link rel="stylesheet" href="../../assest/css/fruta-form-compact.css">

</head>
<body class="sistemRR">
  <div class="container">
    <div class="left-panel">
      <div class="logo">
        <img src="../../assest/img/logo2.png" alt="SmartBerry One">
        <p><a href="https://smartberryone.cl" target="_blank" rel="noopener noreferrer">smartberryone.cl</a></p>
      </div>
      <h2>PORTAL ESTADISTICAS</h2>
      <p class="subtitle">Accede a indicadores y reportes con una experiencia segura, limpia y alineada a SmartBerry One.</p>
      <div class="card">
        <h3>Acceso Estadísticas</h3>
        <form method="post" id="loginForm">
          <input type="text" class="form-input" placeholder="Nombre Usuario" name="NOMBRE" value="<?= $NOMBRE?>" required>
          <input type="password" class="form-input" placeholder="Contraseña" name="CONTRASENA" value="<?= $CONTRASENA?>" required>

          <label for="EMPRESA">Seleccionar Empresa</label>
          <select class="form-input" id="EMPRESA" name="EMPRESA" required>
            <option value="">-- Seleccione Empresa --</option>
            <?php foreach ($ARRAYEMPRESA as $r): ?>
              <option value="<?= $r['ID_EMPRESA']?>" <?= $EMPRESA==$r['ID_EMPRESA']?"selected":""?>><?= $r['NOMBRE_EMPRESA']?></option>
            <?php endforeach;?>
          </select>

          <label for="ESPECIE">Seleccionar Especie</label>
          <select class="form-input" id="ESPECIE" name="ESPECIE" required>
            <option value="">-- Seleccione Especie --</option>
            <option value="1">Arándanos</option>
            <option value="3">Espárragos</option>
          </select>

          <label for="TEMPORADA">Seleccionar Temporada</label>
          <select class="form-input" id="TEMPORADA" name="TEMPORADA" required>

            <?php foreach ($ARRAYTEMPORADA as $r): ?>
              <option value="<?= $r['ID_TEMPORADA']?>" <?= $TEMPORADA==$r['ID_TEMPORADA']?"selected":""?>><?= $r['NOMBRE_TEMPORADA']?></option>
            <?php endforeach;?>
          </select>

          <div class="prefetch-option">
            <input type="checkbox" id="PRECARGA_EXPORTACION_ESTADISTICA" style="margin-top:0;">
            <label for="PRECARGA_EXPORTACION_ESTADISTICA" style="margin-bottom:0;">Cargar información pesada en segundo plano</label>
          </div>

          <div class="security-note">
            <span class="material-icons">lock</span>
            Conexión segura por SSL
          </div>

          <div class="btn-group">
            <a href="../../" class="btn btn-back">Volver</a>
            <button type="submit" class="btn btn-login" name="ENTRAR">Entrar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Panel derecho -->
    <div class="right-panel">
      <div class="slide active" style="background-image:url('../../assest/img/abeja.jpg')"></div>
      <div class="slide" style="background-image:url('../../assest/img/arandano.jpg')"></div>
      <div class="slide" style="background-image:url('../../assest/img/esparragos.jpg')"></div>
    </div>
  </div>

  <script>
    const slides=document.querySelectorAll('.slide');let i=0;
    setInterval(()=>{slides[i].classList.remove('active');i=(i+1)%slides.length;slides[i].classList.add('active')},5000);

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
      loginForm.addEventListener('submit', () => {
        const precarga = document.getElementById('PRECARGA_EXPORTACION_ESTADISTICA')?.checked;
        if (precarga) {
          localStorage.setItem('prefetch_despachoex_exportacion', '1');
        } else {
          localStorage.removeItem('prefetch_despachoex_exportacion');
        }
      });
    }
  </script>

  <?php if ($detect->isMobile() && $detect->isiOS()): ?>
    <script>
      Swal.fire({
        icon:"info",
        title:"iPhone detectado",
        html:"Algunas vistas no están adaptadas 📱<br>Recomendamos usar tablet Android/iPad o PC",
        confirmButtonText:"Vale! 😉"
      })
    </script>
  <?php endif; ?>

  <?php if ($detect->isMobile() && $detect->isAndroidOS()): ?>
    <script>
      Swal.fire({
        icon:"info",
        title:"Android detectado",
        html:"Algunas vistas no están adaptadas 🤖<br>Recomendamos usar tablet Android/iPad o PC",
        confirmButtonText:"Vale! 😉"
      })
    </script>
  <?php endif; ?>

<?php
// === PROCESO DE LOGIN ===
if (isset($_POST['ENTRAR'])) {

    if (empty($_POST['NOMBRE']) || empty($_POST['CONTRASENA'])) {
        echo '<script>
            Swal.fire({
                icon:"info",
                title:"Alerta de inicio de sesión",
                text:"El usuario o contraseña se encuentra vacío, por favor llena los datos mínimos para iniciar sesión",
                confirmButtonText:"OK"
            });
        </script>';
    } else {
        $NOMBRE = $_POST['NOMBRE'];
        $CONTRASENA = $_POST['CONTRASENA'];
        
        $ARRAYINICIOSESSION = $USUARIO_ADO->iniciarSession($NOMBRE, $CONTRASENA);
        if (empty($ARRAYINICIOSESSION)) {
            echo '<script>
                Swal.fire({icon:"error",title:"Error de acceso",text:"Usuario o contraseña incorrectos."});
            </script>';
        } else {
            $ARRAYVERPTUSUARIO = $PTUSUARIO_ADO->listarPtusuarioPorTusuarioCBX($ARRAYINICIOSESSION[0]['ID_TUSUARIO']);
            if ($ARRAYVERPTUSUARIO && $ARRAYVERPTUSUARIO[0]['ESTADISTICA'] == "1") {
                $_SESSION["ID_USUARIO"] = $ARRAYINICIOSESSION[0]['ID_USUARIO'];
                $_SESSION["NOMBRE_USUARIO"] = $ARRAYINICIOSESSION[0]['NOMBRE_USUARIO'];
                $_SESSION["TIPO_USUARIO"] = $ARRAYINICIOSESSION[0]['ID_TUSUARIO'];
                $_SESSION["ID_EMPRESA"] = $_POST['EMPRESA'];
                $_SESSION["ID_TEMPORADA"] = $_POST['TEMPORADA'];
                $_SESSION["ID_ESPECIE"] = $_POST['ESPECIE'];
                echo '<script>
                    Swal.fire({icon:"success",title:"Inicio de sesión correcto",text:"Redirigiendo...",timer:2000,showConfirmButton:false})
                    .then(()=>{location.href="index.php";});
                </script>';
            } else {
                echo '<script>
                    Swal.fire({icon:"warning",title:"Sin permisos",text:"No tiene acceso a este módulo."});
                </script>';
            }
        }
    }
}
?>
</body>
</html>
