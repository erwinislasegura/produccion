<?php
require_once '../../api/vendor/autoload.php';
$detect = new Mobile_Detect;

session_start();
if (isset($_SESSION["ID_USUARIO"], $_SESSION["NOMBRE_USUARIO"], $_SESSION["ID_EMPRESA"], $_SESSION["ID_PLANTA"], $_SESSION["ID_TEMPORADA"])) {
    if ($_SESSION["ID_EMPRESA"] && $_SESSION["ID_PLANTA"] && $_SESSION["ID_TEMPORADA"]) {
        header('Location: index.php');
        exit;
    }
}

include_once '../../assest/controlador/EMPRESA_ADO.php';
include_once '../../assest/controlador/PLANTA_ADO.php';
include_once '../../assest/controlador/TEMPORADA_ADO.php';
include_once "../../assest/controlador/AUSUARIO_ADO.php";

$EMPRESA_ADO = new EMPRESA_ADO();
$PLANTA_ADO = new PLANTA_ADO();
$TEMPORADA_ADO = new TEMPORADA_ADO();
$AUSUARIO_ADO = new AUSUARIO_ADO;

$EMPRESA = $_SESSION["ID_EMPRESA"] ?? "";
$PLANTA = $_SESSION["ID_PLANTA"] ?? "";
$TEMPORADA = $_SESSION["ID_TEMPORADA"] ?? "";
$alertScript = '';

// Generar token CSRF si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Procesamiento del formulario (antes de renderizar HTML para evitar doble selección)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $alertScript = '<script>
          Swal.fire({
            icon:"error",
            title:"Error de seguridad",
            text:"Token CSRF inválido. Recarga la página."
          });
        </script>';
    } else {
        // Validar y sanear entradas
        $empresa = filter_input(INPUT_POST, 'EMPRESA', FILTER_VALIDATE_INT);
        $planta = filter_input(INPUT_POST, 'PLANTA', FILTER_VALIDATE_INT);
        $temporada = filter_input(INPUT_POST, 'TEMPORADA', FILTER_VALIDATE_INT);

        if (!$empresa || !$planta || !$temporada) {
            $alertScript = '<script>
              Swal.fire({
                icon:"warning",
                title:"Campos requeridos",
                text:"Debes seleccionar empresa, planta y temporada válidas"
              });
            </script>';
        } else {
            // Guardar en sesión
            $_SESSION["ID_EMPRESA"] = $empresa;
            $_SESSION["ID_PLANTA"] = $planta;
            $_SESSION["ID_TEMPORADA"] = $temporada;

            // Registrar acción
            $AUSUARIO_ADO->agregarAusuario2(
                'NULL',1,0,"".($_SESSION["NOMBRE_USUARIO"] ?? 'Usuario desconocido').", Selección de parámetros",
                "usuario_usuario",$_SESSION["ID_USUARIO"] ?? 0,$_SESSION["ID_USUARIO"] ?? 0,
                $empresa,$planta,$temporada
            );

            session_regenerate_id(true);
            header('Location: index.php');
            exit;
        }
    }
}

$ARRAYEMPRESA = $EMPRESA_ADO->listarEmpresaCBX();
$ARRAYPLANTA = $PLANTA_ADO->listarPlantaPropiaCBX();
$ARRAYTEMPORADA = $TEMPORADA_ADO->listarTemporadaCBX();

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Selección de Parámetros - SmartBerry One</title>
  <link rel="icon" href="../../assest/img/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="../../assest/js/sweetalert2@11.js"></script>
  <style>
    :root{--vf-berry:#7f1734;--vf-berry-dark:#4d0d20;--vf-leaf:#4f8a2f;--vf-leaf-soft:#eaf4e4;--vf-ink:#263238;--vf-muted:#667085;--vf-border:#e6e9ef;--vf-card:rgba(255,255,255,.92)}
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',sans-serif}
    body,html{min-height:100%;background:#f7f8f5;color:var(--vf-ink)}
    body{background:radial-gradient(circle at top left,rgba(127,23,52,.16),transparent 32%),linear-gradient(135deg,#fff 0%,#f5f8ef 100%)}
    .container{display:flex;min-height:100vh;position:relative;overflow:hidden}.left-panel{width:min(100%,480px);background:var(--vf-card);backdrop-filter:blur(18px);padding:48px 42px;display:flex;flex-direction:column;justify-content:center;box-shadow:24px 0 70px rgba(77,13,32,.10);z-index:2}.logo{text-align:center;margin-bottom:26px}.logo img{max-width:220px;width:72%;height:auto}.logo p{color:var(--vf-muted);font-size:13px;margin-top:12px}.logo p a{color:var(--vf-berry);text-decoration:none;font-weight:600}.logo p a:hover{text-decoration:underline}h2{text-align:center;color:var(--vf-berry-dark);font-size:20px;margin-bottom:8px;font-weight:800;letter-spacing:.04em}.subtitle{text-align:center;color:var(--vf-muted);font-size:14px;line-height:1.5;margin-bottom:24px}form{width:100%;background:#fff;border:1px solid rgba(127,23,52,.11);border-radius:24px;padding:24px;box-shadow:0 22px 50px rgba(77,13,32,.10)}label{font-size:13px;color:var(--vf-berry-dark);margin-bottom:7px;display:block;font-weight:800}.form-input{width:100%;padding:14px 15px;border:1px solid var(--vf-border);border-radius:14px;margin-bottom:16px;font-size:14px;background:#fbfcfb;transition:border-color .2s,box-shadow .2s,background .2s}.form-input:focus{outline:none;border-color:var(--vf-berry);box-shadow:0 0 0 4px rgba(127,23,52,.12);background:#fff}.btn{width:100%;padding:14px;border:none;border-radius:14px;font-weight:800;cursor:pointer;margin-top:10px;text-decoration:none;display:block;text-align:center;transition:transform .2s,box-shadow .2s,background .2s}.btn:hover{transform:translateY(-1px)}.btn-login{background:linear-gradient(135deg,var(--vf-berry),#a52749);color:#fff;box-shadow:0 14px 28px rgba(127,23,52,.24)}.btn-login:hover{background:linear-gradient(135deg,var(--vf-berry-dark),var(--vf-berry))}.btn-back{background:#fff;color:var(--vf-berry-dark);border:1px solid rgba(127,23,52,.18);box-shadow:0 12px 26px rgba(77,13,32,.08)}.right-panel{flex:1;position:relative;overflow:hidden;background:#1f2a1d}.right-panel:after{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(77,13,32,.28),rgba(22,44,19,.12)),radial-gradient(circle at 78% 18%,rgba(255,255,255,.20),transparent 28%);z-index:1}.slide{position:absolute;inset:0;background-size:cover;background-position:center;opacity:0;transform:scale(1.04);transition:opacity 1.5s,transform 6s}.slide.active{opacity:1;transform:scale(1)}@media(max-width:820px){.container{flex-direction:column}.left-panel{width:100%;padding:34px 22px}.right-panel{height:280px;order:-1}.logo img{max-width:190px}}
  </style>
  <link rel="stylesheet" href="../../assest/css/fruta-form-compact.css">

</head>
<body class="sistemRR">
  <div class="container">
    <!-- Panel izquierdo -->
    <div class="left-panel">
      <div class="logo">
        <img src="../../assest/img/logo2.png" alt="SmartBerry One">
        <p><a href="https://smartberryone.cl" target="_blank" rel="noopener noreferrer" style="color:#555; text-decoration:none;">smartberryone.cl</a></p>
      </div>
      <h2>Selección de Parámetros</h2>
      <p class="subtitle">Elige empresa, planta y temporada para continuar con tu operación.</p>
      
      <form method="post" id="form_reg_dato" novalidate>
        <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
        
        <label for="EMPRESA">Seleccionar Empresa</label>
        <select class="form-input" id="EMPRESA" name="EMPRESA" required>
          <option value="">-- Seleccione Empresa --</option>
          <?php foreach ($ARRAYEMPRESA as $r): ?>
            <option value="<?= h($r['ID_EMPRESA']) ?>" <?= $EMPRESA == $r['ID_EMPRESA'] ? "selected" : "" ?>><?= h($r['NOMBRE_EMPRESA']) ?></option>
          <?php endforeach;?>
        </select>

        <label for="PLANTA">Seleccionar Planta</label>
        <select class="form-input" id="PLANTA" name="PLANTA" required>
          <option value="">-- Seleccione Planta --</option>
          <?php foreach ($ARRAYPLANTA as $r): ?>
            <option value="<?= h($r['ID_PLANTA']) ?>" <?= $PLANTA == $r['ID_PLANTA'] ? "selected" : "" ?>><?= h($r['NOMBRE_PLANTA']) ?></option>
          <?php endforeach;?>
        </select>

        <label for="TEMPORADA">Seleccionar Temporada</label>
        <select class="form-input" id="TEMPORADA" name="TEMPORADA" required>
          <option value="">-- Seleccione Temporada --</option>
          <?php foreach ($ARRAYTEMPORADA as $r): ?>
            <option value="<?= h($r['ID_TEMPORADA']) ?>" <?= $TEMPORADA == $r['ID_TEMPORADA'] ? "selected" : "" ?>><?= h($r['NOMBRE_TEMPORADA']) ?></option>
          <?php endforeach;?>
        </select>

        <button type="submit" class="btn btn-login" name="ENTRAR">Ingresar</button>
      </form>

      <!-- Botón atrás fuera del formulario -->
      <a href="../../" class="btn btn-back">Atrás</a>
    </div>

    <!-- Panel derecho con slider -->
    <div class="right-panel">
      <div class="slide active" style="background-image:url('../../assest/img/abeja.jpg')"></div>
      <div class="slide" style="background-image:url('../../assest/img/arandano.jpg')"></div>
      <div class="slide" style="background-image:url('../../assest/img/esparragos.jpg')"></div>
    </div>
  </div>

  <script>
    // Slider automático
    const slides=document.querySelectorAll('.slide');let i=0;
    setInterval(()=>{slides[i].classList.remove('active');i=(i+1)%slides.length;slides[i].classList.add('active')},5000);
  </script>

  <?= $alertScript ?>
</body>
</html>
