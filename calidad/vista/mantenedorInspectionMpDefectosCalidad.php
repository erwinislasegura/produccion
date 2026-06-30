<?php
include_once __DIR__ . '/../../assest/config/validarUsuarioFruta.php';
include_once __DIR__ . '/../../assest/config/BDCONFIG.php';
$cfg = new BDCONFIG();
$conexion = new PDO('mysql:host=' . $cfg->__GET('HOST') . ';dbname=' . $cfg->__GET('DBNAME'), $cfg->__GET('USER'), $cfg->__GET('PASS'));
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$conexion->exec("CREATE TABLE IF NOT EXISTS calidad_defectos_calidad (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120) NOT NULL,defect_type ENUM('critical','major','minor') NOT NULL DEFAULT 'minor',is_active TINYINT(1) NOT NULL DEFAULT 1,defect_id INT NULL,created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB");
$defectsTableExists = (bool)$conexion->query("SHOW TABLES LIKE 'defects'")->fetch(PDO::FETCH_NUM);
if (!$defectsTableExists) { $conexion->exec("CREATE TABLE defects (id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(120) NOT NULL,defect_type ENUM('major','minor') NOT NULL DEFAULT 'minor',is_active TINYINT(1) NOT NULL DEFAULT 1)"); }
$hasScope = (bool)$conexion->query("SHOW COLUMNS FROM defects LIKE 'defect_scope'")->fetch(PDO::FETCH_ASSOC);
$defTypeInfo = $conexion->query("SHOW COLUMNS FROM defects LIKE 'defect_type'")->fetch(PDO::FETCH_ASSOC);
$supportsCritical = isset($defTypeInfo['Type']) && stripos($defTypeInfo['Type'], 'critical') !== false;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$accion = $_GET['a'] ?? '';
$disabled = ($accion === 'ver') ? 'disabled' : '';
$name=''; $type='minor';
if ($accion==='0' && $id>0) {
  $row=$conexion->prepare("SELECT defect_id FROM calidad_defectos_calidad WHERE id=:id");$row->execute([':id'=>$id]);$x=$row->fetch(PDO::FETCH_ASSOC);
  $conexion->prepare("UPDATE calidad_defectos_calidad SET is_active=0 WHERE id=:id")->execute([':id'=>$id]);
  if($x && !empty($x['defect_id'])){$conexion->prepare("UPDATE defects SET is_active=0 WHERE id=:id")->execute([':id'=>(int)$x['defect_id']]);}
  header('Location: mantenedorInspectionMpDefectosCalidad.php');exit;
}
if($id>0){$st=$conexion->prepare("SELECT * FROM calidad_defectos_calidad WHERE id=:id LIMIT 1");$st->execute([':id'=>$id]);$r=$st->fetch(PDO::FETCH_ASSOC);if($r){$name=$r['name'];$type=$r['defect_type'];}}
if(isset($_POST['GUARDAR'])){
  $idForm=(int)($_POST['id']??0); $name=trim($_POST['name']??''); $type=trim($_POST['defect_type']??'minor');
  if($name!==''){
    if($idForm>0){
      $conexion->prepare("UPDATE calidad_defectos_calidad SET name=:name, defect_type=:type, is_active=1 WHERE id=:id")->execute([':name'=>$name,':type'=>$type,':id'=>$idForm]);
    } else {
      $masterType = (!$supportsCritical && $type==='critical') ? 'major' : $type;
      if($hasScope){$conexion->prepare("INSERT INTO defects (name, defect_type, defect_scope, is_active) VALUES (:name,:type,'quality',1)")->execute([':name'=>$name,':type'=>$masterType]);}
      else {$conexion->prepare("INSERT INTO defects (name, defect_type, is_active) VALUES (:name,:type,1)")->execute([':name'=>$name,':type'=>$masterType]);}
      $masterId=(int)$conexion->lastInsertId();
      $conexion->prepare("INSERT INTO calidad_defectos_calidad (name, defect_type, is_active, defect_id) VALUES (:name,:type,1,:defect_id)")->execute([':name'=>$name,':type'=>$type,':defect_id'=>$masterId]);
    }
  }
  header('Location: mantenedorInspectionMpDefectosCalidad.php');exit;
}
$rows=$conexion->query("SELECT id,name,defect_type,is_active FROM calidad_defectos_calidad ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Defectos de Calidad</title>
    <?php include_once __DIR__ . '/../../assest/config/urlHead.php'; ?>
    <style>
        .mp-panel {
            border-radius: 10px;
            border: 1px solid #dbe4f2;
            box-shadow: 0 4px 14px rgba(31, 53, 90, 0.08);
            background: #fff;
            overflow: hidden;
        }

        .mp-panel .box-header {
            background: #f7f9fc;
            border-bottom: 1px solid #e7edf7;
        }

        .mp-title {
            font-weight: 700;
            color: #24324a !important;
        }

        .mp-subtitle {
            margin: 3px 0 0;
            color: #6b7a94;
            font-size: 12px;
        }

        .mp-form-label {
            font-size: 12px;
            font-weight: 700;
            color: #4a5872;
            margin-bottom: 6px;
        }

        .mp-input {
            border-radius: 8px;
            border: 1px solid #cfdaea;
            min-height: 40px;
            color: #24324a;
        }

        .mp-input:focus {
            border-color: #5a8de6;
            box-shadow: 0 0 0 0.15rem rgba(54, 113, 220, .15);
        }

        .mp-btn-primary {
            background: #1f6fe5;
            color: #fff;
            border-radius: 8px;
            border: 0;
            font-weight: 700;
        }

        .mp-btn-light {
            background: #edf2fb;
            color: #3e4f6b;
            border-radius: 8px;
            border: 0;
            font-weight: 700;
        }

        .mp-table {
            margin-bottom: 0;
        }

        .mp-table thead th {
            background: #f4f7fc;
            color: #4d5d79 !important;
            border-bottom: 1px solid #e2e9f5;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .mp-table tbody td {
            color: #22314a !important;
            background: #fff;
            border-top: 1px solid #e8edf6;
            vertical-align: middle;
        }

        .mp-badge {
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
        }

        .mp-badge-active {
            background: #e8f6ee;
            color: #187a4f;
        }

        .mp-badge-inactive {
            background: #fbeaea;
            color: #b63636;
        }

        .mp-actions .btn {
            border-radius: 6px;
            margin: 2px 0;
            font-weight: 700;
        }
    </style>
</head>
<body class="hold-transition light-skin fixed sidebar-mini theme-primary sistemRR">
    <div class="wrapper">
        <?php include_once __DIR__ . '/../../assest/config/menuCalidad.php'; ?>

        <div class="content-wrapper">
            <div class="container-full">
                <div class="content-header">
                    <div class="d-flex align-items-center">
                        <div class="mr-auto">
                            <h3 class="page-title">Mantenedor - Defectos de Calidad</h3>
                        </div>
                        <?php include_once __DIR__ . '/../../assest/config/verIndicadorEconomico.php'; ?>
                    </div>
                </div>

                <section class="content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="box mp-panel">
                                <div class="box-header with-border">
                                    <h4 class="box-title mp-title">Formulario</h4>
                                    <p class="mp-subtitle">Ingrese o edite un defecto</p>
                                </div>
                                <div class="box-body">
                                    <form class="form-one-line" data-form-layout="oneline-1" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.65rem;padding:0.65rem 0;border-bottom:1px solid #3a3a3a;" method="post">
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">

                                        <div class="form-group">
                                            <label class="mp-form-label">Nombre Defecto</label>
                                            <input <?php echo $disabled; ?> type="text" name="name" class="form-control mp-input" value="<?php echo htmlspecialchars($name); ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label class="mp-form-label">Tipo</label>
                                            <select <?php echo $disabled; ?> name="defect_type" class="form-control mp-input">
                                                <option value="critical" <?php echo $type==='critical'?'selected':''; ?>>Crítico</option>
                                                <option value="major" <?php echo $type==='major'?'selected':''; ?>>Mayor</option>
                                                <option value="minor" <?php echo $type==='minor'?'selected':''; ?>>Menor</option>
                                            </select>
                                        </div>

                                        <button <?php echo $disabled; ?> class="btn mp-btn-primary" name="GUARDAR" value="1">Guardar</button>
                                        <a class="btn mp-btn-light" href="mantenedorInspectionMpDefectosCalidad.php">Nuevo</a>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="box mp-panel">
                                <div class="box-header with-border">
                                    <h4 class="box-title mp-title">Registros existentes</h4>
                                    <p class="mp-subtitle">Listado actual de defectos</p>
                                </div>
                                <div class="box-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mp-table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Defecto</th>
                                                    <th>Tipo</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($rows)): ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center">No hay registros disponibles.</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($rows as $r): ?>
                                                        <tr>
                                                            <td>#<?php echo $r['id']; ?></td>
                                                            <td><?php echo htmlspecialchars($r['name']); ?></td>
                                                            <td><?php echo htmlspecialchars($r['defect_type']); ?></td>
                                                            <td>
                                                                <?php if ((int) $r['is_active'] === 1): ?>
                                                                    <span class="mp-badge mp-badge-active">Activo</span>
                                                                <?php else: ?>
                                                                    <span class="mp-badge mp-badge-inactive">Inactivo</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="mp-actions">
                                                                <a class="btn btn-xs btn-info" href="?id=<?php echo $r['id']; ?>&a=ver">Ver</a>
                                                                <a class="btn btn-xs btn-warning" href="?id=<?php echo $r['id']; ?>&a=editar">Editar</a>
                                                                <?php if ((int) $r['is_active'] === 1): ?>
                                                                    <a class="btn btn-xs btn-danger" href="?id=<?php echo $r['id']; ?>&a=0" onclick="return confirm('¿Desactivar registro?');">Eliminar</a>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <?php include_once __DIR__ . '/../../assest/config/footer.php'; ?>
        <?php include_once __DIR__ . '/../../assest/config/menuExtraFruta.php'; ?>
    </div>
    <?php include_once __DIR__ . '/../../assest/config/urlBase.php'; ?>
</body>
</html>
