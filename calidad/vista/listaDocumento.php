<?php
$rootPath = __DIR__ . "/../../";
include_once $rootPath . "assest/config/validarUsuarioFruta.php";
include_once $rootPath . "assest/controlador/productor_controller.php";

$id = isset($_REQUEST['id']) ? (int)base64_decode($_REQUEST['id']) : 0;
$productorController = new ProductorController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_documento'])) {
    $id_documento = (int)$_POST['id_documento'];
    $productorController->deleteDocumento($id_documento);
    header("Location: listaDocumento.php?id=" . base64_encode($id));
    exit();
}

$documentos = $id > 0 ? $productorController->viewDocumentos($id) : [];
$totalDocumentos = is_array($documentos) ? count($documentos) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Calidad - Lista Documentos</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="">
    <meta name="author" content="">
    <?php include_once $rootPath . "assest/config/urlHead.php"; ?>
    <style>
        body.sistemRR { background:#f4f7fb; color:#2f3b4c; }
        .content-header { padding:8px 0 0; margin-bottom:4px; }
        .content-header .page-title { font-size:16px; font-weight:600; color:#1f2d3d; margin-bottom:0; }
        .doc-panel { border:1px solid #d8e1ec; border-radius:10px; box-shadow:0 2px 8px rgba(17,32,51,.04); overflow:hidden; background:#fff; }
        .doc-panel .box-header { background:#eff3f8; border-bottom:1px solid #dbe3ee; padding:10px 14px; }
        .doc-panel .box-title { color:#2d4057; font-size:14px; font-weight:600; margin:0; }
        .doc-panel .box-body { padding:12px 14px; }
        .doc-toolbar { display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; }
        .doc-summary { border:1px solid #dce5ef; border-top:3px solid #43a95c; border-radius:10px; background:#fff; padding:10px; margin-bottom:12px; max-width:280px; }
        .doc-summary__label { font-size:11px; text-transform:uppercase; letter-spacing:.6px; color:#6f8197; font-weight:600; }
        .doc-summary__value { font-size:24px; line-height:1; color:#24384f; font-weight:600; margin-top:5px; }
        .doc-table { width:100%; }
        .doc-table thead th { background:#f4f7fc; color:#4d5d79 !important; border-bottom:1px solid #e2e9f5; font-size:12px; text-transform:uppercase; letter-spacing:.03em; vertical-align:middle; }
        .doc-table tbody td { color:#22314a !important; border-top:1px solid #e8edf6; vertical-align:middle; }
        .doc-delete-form { display:inline; }
        .doc-btn { border-radius:999px !important; font-weight:600; letter-spacing:.1px; padding:7px 14px; box-shadow:0 1px 3px rgba(31,53,90,.08); display:inline-flex; align-items:center; justify-content:center; gap:6px; }
        .doc-btn:hover { transform:translateY(-1px); box-shadow:0 3px 8px rgba(31,53,90,.12); }
        .doc-actions-inline { display:flex; gap:6px; flex-wrap:wrap; }
        @media (max-width: 767px) { .doc-btn { width:100%; margin-top:4px; } .doc-actions-inline { width:100%; } }
    </style>
</head>
<body class="hold-transition light-skin fixed sidebar-mini theme-primary sistemRR">
    <div class="wrapper">
        <?php include_once $rootPath . "assest/config/menuCalidad.php"; ?>
        <div class="content-wrapper">
            <div class="container-full">
                <div class="content-header">
                    <div class="d-flex align-items-center">
                        <div class="mr-auto">
                            <h3 class="page-title">Dashboard · Lista Documentos</h3>
                        </div>
                        <?php include_once $rootPath . "assest/config/verIndicadorEconomico.php"; ?>
                    </div>
                </div>
                <section class="content">
                    <div class="doc-summary"><div class="doc-summary__label">Documentos del productor</div><div class="doc-summary__value"><?php echo $totalDocumentos; ?></div></div>
                    <div class="box doc-panel">
                        <div class="box-header with-border">
                            <div class="doc-toolbar">
                                <h4 class="box-title">Documentos asociados</h4>
                                <div>
                                    <a href="listarProductorDocumento.php" class="btn btn-outline btn-secondary doc-btn"><i class="ti-back-left"></i> Volver</a>
                                    <a href="addDocumentoProductor.php" class="btn btn-success doc-btn"><i class="ti-plus"></i> Agregar documento</a>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="tabla-documentos" class="table table-hover doc-table">
                                    <thead>
                                        <tr>
                                            <th>Archivo</th>
                                            <th>Nombre</th>
                                            <th>Vigencia</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($documentos)): ?>
                                            <?php foreach ($documentos as $documento): ?>
                                                <tr>
                                                    <td><a href="../../data/data_productor/<?php echo htmlspecialchars($documento->archivo_documento); ?>" target="_blank" class="btn btn-info doc-btn"><i class="ti-file"></i> Ver archivo</a></td>
                                                    <td><?php echo htmlspecialchars($documento->nombre_documento); ?></td>
                                                    <td><?php echo htmlspecialchars($documento->vigencia_documento); ?></td>
                                                    <td>
                                                        <form class="doc-delete-form" method="POST">
                                                            <input type="hidden" name="id_documento" value="<?php echo (int)$documento->id_documento; ?>">
                                                            <button type="submit" class="btn btn-danger doc-btn" onclick="return confirm('¿Estás seguro de que deseas eliminar este documento?');"><i class="ti-trash"></i> Eliminar</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center text-muted">No hay documentos disponibles para este productor.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php include_once $rootPath . "assest/config/footer.php"; ?>
        <?php include_once $rootPath . "assest/config/menuExtraFruta.php"; ?>
    </div>
    <?php include_once $rootPath . "assest/config/urlBase.php"; ?>
    <script>
        $('#tabla-documentos').DataTable({ ordering: false, paging: true, searching: true });
    </script>
</body>
</html>
