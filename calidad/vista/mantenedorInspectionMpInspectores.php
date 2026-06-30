<?php
include_once __DIR__ . '/../../assest/config/validarUsuarioFruta.php';
include_once __DIR__ . '/../../assest/config/BDCONFIG.php';

$cfg = new BDCONFIG();
$conexion = new PDO(
    'mysql:host=' . $cfg->__GET('HOST') . ';dbname=' . $cfg->__GET('DBNAME'),
    $cfg->__GET('USER'),
    $cfg->__GET('PASS')
);
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function hasCol(PDO $db, $table, $col)
{
    $st = $db->prepare("SHOW COLUMNS FROM `{$table}` LIKE :col");
    $st->execute([':col' => $col]);
    return (bool) $st->fetch(PDO::FETCH_ASSOC);
}

function pickExistingCol(PDO $db, $table, array $candidates)
{
    foreach ($candidates as $col) {
        if (hasCol($db, $table, $col)) {
            return $col;
        }
    }
    return null;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$accion = $_GET['a'] ?? '';
$disabled = ($accion === 'ver') ? 'disabled' : '';
$nombre = '';
$telefono = '';
$correo = '';
$origen = 'propio';

$colTelefono = pickExistingCol($conexion, 'fruta_inpector', ['TELEFONO_INPECTOR', 'FONO_INPECTOR', 'TELEFONO']);
$colCorreo = pickExistingCol($conexion, 'fruta_inpector', ['CORREO_INPECTOR', 'EMAIL_INPECTOR', 'CORREO', 'EMAIL']);
$colOrigen = pickExistingCol($conexion, 'fruta_inpector', ['ORIGEN_INPECTOR', 'TIPO_INPECTOR', 'PROCEDENCIA_INPECTOR']);

if ($accion === '0' && $id > 0) {
    $conexion->prepare('UPDATE fruta_inpector SET ESTADO_REGISTRO = 0 WHERE ID_INPECTOR = :id')->execute([':id' => $id]);
    header('Location: mantenedorInspectionMpInspectores.php');
    exit;
}

if ($id > 0) {
    $fields = ['ID_INPECTOR id', 'NOMBRE_INPECTOR name'];
    $fields[] = $colTelefono ? "{$colTelefono} phone" : "'' phone";
    $fields[] = $colCorreo ? "{$colCorreo} email" : "'' email";
    $fields[] = $colOrigen ? "{$colOrigen} origin_type" : "'propio' origin_type";

    $st = $conexion->prepare('SELECT ' . implode(', ', $fields) . ' FROM fruta_inpector WHERE ID_INPECTOR = :id LIMIT 1');
    $st->execute([':id' => $id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $nombre = $row['name'] ?? '';
        $telefono = $row['phone'] ?? '';
        $correo = $row['email'] ?? '';
        $origen = ($row['origin_type'] ?? 'propio') === 'externa' ? 'externa' : 'propio';
    }
}

if (isset($_POST['GUARDAR'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $origen = ($_POST['origen'] ?? 'propio') === 'externa' ? 'externa' : 'propio';
    $idForm = (int) ($_POST['id'] ?? 0);

    if ($nombre !== '') {
        if ($idForm > 0) {
            $setParts = ['NOMBRE_INPECTOR = :nombre', 'ESTADO_REGISTRO = 1'];
            $params = [':nombre' => $nombre, ':id' => $idForm];

            if ($colTelefono) {
                $setParts[] = $colTelefono . ' = :telefono';
                $params[':telefono'] = $telefono;
            }
            if ($colCorreo) {
                $setParts[] = $colCorreo . ' = :correo';
                $params[':correo'] = $correo;
            }
            if ($colOrigen) {
                $setParts[] = $colOrigen . ' = :origen';
                $params[':origen'] = $origen;
            }
            if (hasCol($conexion, 'fruta_inpector', 'MODIFICACION')) {
                $setParts[] = 'MODIFICACION = :modificacion';
                $params[':modificacion'] = date('Y-m-d');
            }

            $sql = 'UPDATE fruta_inpector SET ' . implode(', ', $setParts) . ' WHERE ID_INPECTOR = :id';
            $conexion->prepare($sql)->execute($params);
        } else {
            $cols = ['NOMBRE_INPECTOR', 'ESTADO_REGISTRO'];
            $params = [':nombre' => $nombre, ':estado_registro' => 1];

            if ($colTelefono) {
                $cols[] = $colTelefono;
                $params[':telefono'] = $telefono;
            }
            if ($colCorreo) {
                $cols[] = $colCorreo;
                $params[':correo'] = $correo;
            }
            if ($colOrigen) {
                $cols[] = $colOrigen;
                $params[':origen'] = $origen;
            }
            if (hasCol($conexion, 'fruta_inpector', 'ID_EMPRESA')) {
                $cols[] = 'ID_EMPRESA';
                $params[':id_empresa'] = (int) ($_SESSION['ID_EMPRESA'] ?? 0);
            }
            if (hasCol($conexion, 'fruta_inpector', 'ID_USUARIOI')) {
                $cols[] = 'ID_USUARIOI';
                $params[':id_usuarioi'] = (int) ($_SESSION['ID_USUARIO'] ?? 0);
            }
            if (hasCol($conexion, 'fruta_inpector', 'ID_USUARIOM')) {
                $cols[] = 'ID_USUARIOM';
                $params[':id_usuariom'] = (int) ($_SESSION['ID_USUARIO'] ?? 0);
            }
            if (hasCol($conexion, 'fruta_inpector', 'INGRESO')) {
                $cols[] = 'INGRESO';
                $params[':ingreso'] = date('Y-m-d');
            }
            if (hasCol($conexion, 'fruta_inpector', 'MODIFICACION')) {
                $cols[] = 'MODIFICACION';
                $params[':modificacion'] = date('Y-m-d');
            }

            $pm = [
                'NOMBRE_INPECTOR' => ':nombre',
                'ESTADO_REGISTRO' => ':estado_registro',
                'ID_EMPRESA' => ':id_empresa',
                'ID_USUARIOI' => ':id_usuarioi',
                'ID_USUARIOM' => ':id_usuariom',
                'INGRESO' => ':ingreso',
                'MODIFICACION' => ':modificacion',
            ];
            if ($colTelefono) {
                $pm[$colTelefono] = ':telefono';
            }
            if ($colCorreo) {
                $pm[$colCorreo] = ':correo';
            }
            if ($colOrigen) {
                $pm[$colOrigen] = ':origen';
            }

            $ph = array_map(fn($c) => $pm[$c], $cols);
            $sql = 'INSERT INTO fruta_inpector (' . implode(',', $cols) . ') VALUES (' . implode(',', $ph) . ')';
            $conexion->prepare($sql)->execute($params);
        }
    }

    header('Location: mantenedorInspectionMpInspectores.php');
    exit;
}

$whereEmpresa = '';
if (hasCol($conexion, 'fruta_inpector', 'ID_EMPRESA') && !empty($_SESSION['ID_EMPRESA'])) {
    $whereEmpresa = ' WHERE ID_EMPRESA=' . (int) $_SESSION['ID_EMPRESA'];
}

$listFields = ['ID_INPECTOR id', 'NOMBRE_INPECTOR name', 'ESTADO_REGISTRO estado'];
$listFields[] = $colTelefono ? "{$colTelefono} phone" : "'' phone";
$listFields[] = $colCorreo ? "{$colCorreo} email" : "'' email";
$listFields[] = $colOrigen ? "{$colOrigen} origin_type" : "'propio' origin_type";

$inspectores = $conexion
    ->query('SELECT ' . implode(', ', $listFields) . " FROM fruta_inpector{$whereEmpresa} ORDER BY ID_INPECTOR DESC")
    ->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Mantenedor Inspectores</title>
    <?php include_once __DIR__ . '/../../assest/config/urlHead.php'; ?>
    <style>
        .mp-panel { border-radius: 10px; border: 1px solid #dbe4f2; box-shadow: 0 4px 14px rgba(31, 53, 90, 0.08); background: #fff; overflow: hidden; }
        .mp-panel .box-header { background: #f7f9fc; border-bottom: 1px solid #e7edf7; }
        .mp-title { font-weight: 700; color: #24324a !important; }
        .mp-subtitle { margin: 3px 0 0; color: #6b7a94; font-size: 12px; }
        .mp-form-label { font-size: 12px; font-weight: 700; color: #4a5872; margin-bottom: 6px; }
        .mp-input { border-radius: 8px; border: 1px solid #cfdaea; min-height: 40px; color: #24324a; }
        .mp-input:focus { border-color: #5a8de6; box-shadow: 0 0 0 0.15rem rgba(54, 113, 220, .15); }
        .mp-btn-primary { background: #1f6fe5; color: #fff; border-radius: 8px; border: 0; font-weight: 700; }
        .mp-btn-light { background: #edf2fb; color: #3e4f6b; border-radius: 8px; border: 0; font-weight: 700; }
        .mp-table { margin-bottom: 0; }
        .mp-table thead th { background: #f4f7fc; color: #4d5d79 !important; border-bottom: 1px solid #e2e9f5; font-size: 12px; text-transform: uppercase; letter-spacing: .03em; }
        .mp-table tbody td { color: #22314a !important; background: #fff; border-top: 1px solid #e8edf6; vertical-align: middle; }
        .mp-badge { border-radius: 999px; font-size: 11px; font-weight: 700; padding: 4px 10px; }
        .mp-badge-active { background: #e8f6ee; color: #187a4f; }
        .mp-badge-inactive { background: #fbeaea; color: #b63636; }
        .mp-actions .btn { border-radius: 6px; margin: 2px 0; font-weight: 700; }
    </style>
</head>

<body class="hold-transition light-skin fixed sidebar-mini theme-primary sistemRR">
    <div class="wrapper">
        <?php include_once __DIR__ . '/../../assest/config/menuCalidad.php'; ?>
        <div class="content-wrapper">
            <div class="container-full">
                <div class="content-header"><div class="d-flex align-items-center"><div class="mr-auto"><h3 class="page-title">Mantenedor - Inspectores</h3></div><?php include_once __DIR__ . '/../../assest/config/verIndicadorEconomico.php'; ?></div></div>
                <section class="content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="box mp-panel">
                                <div class="box-header with-border"><h4 class="box-title mp-title">Formulario Inspector</h4><p class="mp-subtitle">Ingrese o edite un inspector</p></div>
                                <div class="box-body">
                                    <form class="form-one-line" data-form-layout="oneline-1" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.65rem;padding:0.65rem 0;border-bottom:1px solid #3a3a3a;" method="post">
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                        <div class="form-group"><label class="mp-form-label">Nombre del inspector</label><input <?php echo $disabled; ?> type="text" name="nombre" class="form-control mp-input" value="<?php echo htmlspecialchars($nombre); ?>" required></div>
                                        <div class="form-group"><label class="mp-form-label">Número de teléfono</label><input <?php echo $disabled; ?> type="text" name="telefono" class="form-control mp-input" value="<?php echo htmlspecialchars($telefono); ?>"></div>
                                        <div class="form-group"><label class="mp-form-label">Correo electrónico</label><input <?php echo $disabled; ?> type="email" name="correo" class="form-control mp-input" value="<?php echo htmlspecialchars($correo); ?>"></div>
                                        <div class="form-group"><label class="mp-form-label">Tipo de inspector</label><select <?php echo $disabled; ?> name="origen" class="form-control mp-input"><option value="propio" <?php echo $origen === 'propio' ? 'selected' : ''; ?>>Propio</option><option value="externa" <?php echo $origen === 'externa' ? 'selected' : ''; ?>>Empresa externa</option></select></div>
                                        <button <?php echo $disabled; ?> class="btn mp-btn-primary" name="GUARDAR" value="1">Guardar</button>
                                        <a class="btn mp-btn-light" href="mantenedorInspectionMpInspectores.php">Nuevo</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="box mp-panel">
                                <div class="box-header with-border"><h4 class="box-title mp-title">Registros existentes</h4><p class="mp-subtitle">Listado actual de inspectores</p></div>
                                <div class="box-body p-0"><div class="table-responsive"><table class="table table-hover mp-table"><thead><tr><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Correo</th><th>Origen</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>
                                    <?php if (empty($inspectores)): ?><tr><td colspan="7" class="text-center">No hay registros disponibles.</td></tr><?php else: foreach ($inspectores as $r): ?>
                                    <tr>
                                        <td>#<?php echo $r['id']; ?></td>
                                        <td><?php echo htmlspecialchars($r['name']); ?></td>
                                        <td><?php echo htmlspecialchars($r['phone'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($r['email'] ?? ''); ?></td>
                                        <td><?php echo (($r['origin_type'] ?? 'propio') === 'externa') ? 'Empresa externa' : 'Propio'; ?></td>
                                        <td><?php if ((int) $r['estado'] === 1): ?><span class="mp-badge mp-badge-active">Activo</span><?php else: ?><span class="mp-badge mp-badge-inactive">Inactivo</span><?php endif; ?></td>
                                        <td class="mp-actions"><a class="btn btn-xs btn-info" href="?id=<?php echo $r['id']; ?>&a=ver">Ver</a> <a class="btn btn-xs btn-warning" href="?id=<?php echo $r['id']; ?>&a=editar">Editar</a> <?php if ((int) $r['estado'] === 1): ?><a class="btn btn-xs btn-danger" href="?id=<?php echo $r['id']; ?>&a=0" onclick="return confirm('¿Desactivar registro?');">Eliminar</a><?php endif; ?></td>
                                    </tr>
                                    <?php endforeach; endif; ?>
                                </tbody></table></div></div>
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
