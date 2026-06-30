<?php
$rootPath = __DIR__ . '/../../';
include_once $rootPath . 'assest/config/validarUsuarioFruta.php';
include_once $rootPath . 'assest/controlador/InspectionMPController.php';

$controller = new InspectionMPController();
$result = null;
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    try {
        $result = $controller->importFromExcel($_FILES['excel_file']['tmp_name'], $IDUSUARIOS);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Importar Inspección MP</title>
    <?php include_once $rootPath . 'assest/config/urlHead.php'; ?>
    <style>
        body.sistemRR { background:#f4f7fb; color:#2f3b4c; }
        .content-header { padding:8px 0 0; margin-bottom:4px; }
        .content-header .page-title { font-size:16px; font-weight:600; color:#1f2d3d; margin-bottom:0; }
        .import-panel { border:1px solid #d8e1ec; border-radius:10px; box-shadow:0 2px 8px rgba(17,32,51,.04); overflow:hidden; background:#fff; }
        .import-panel .box-header { background:#eff3f8; border-bottom:1px solid #dbe3ee; padding:10px 14px; }
        .import-panel .box-title { font-size:14px; font-weight:600; color:#2d4057; }
        .import-panel .box-body { padding:18px; }
        .import-dropzone { border:1px dashed #9aaac0; border-radius:10px; background:#f8fafc; padding:22px; text-align:center; }
        .import-dropzone label { color:#2d4057; font-weight:700; }
        .import-dropzone .form-control { max-width:420px; margin:10px auto 0; border-radius:8px; }
        .import-actions { background:#f7f9fc; border-top:1px solid #e1e8f2; padding:12px 14px; }
    </style>
</head>
<body class="hold-transition light-skin fixed sidebar-mini theme-primary sistemRR">
<div class="wrapper">
    <?php include_once $rootPath . 'assest/config/menuCalidad.php'; ?>
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="mr-auto"><h3 class="page-title">Dashboard · Importar Inspección MP</h3></div>
                    <?php include_once $rootPath . 'assest/config/verIndicadorEconomico.php'; ?>
                </div>
            </div>
            <section class="content">
                <div class="box import-panel">
                    <div class="box-header with-border">
                        <h4 class="box-title mb-0">Carga masiva desde Excel</h4>
                    </div>
                    <form method="post" enctype="multipart/form-data">
                        <div class="box-body">
                            <div class="import-dropzone">
                                <label>Seleccione el archivo Excel de inspecciones MP</label>
                                <p class="text-muted mb-0">Formatos permitidos: .xlsx y .xls</p>
                                <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required>
                            </div>
                        </div>
                        <div class="import-actions">
                            <button type="submit" class="btn btn-success btn-rounded">Importar</button>
                            <a href="index.php" class="btn btn-outline btn-secondary btn-rounded">Volver</a>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
    <?php include_once $rootPath . 'assest/config/footer.php'; ?>
    <?php include_once $rootPath . 'assest/config/menuExtraFruta.php'; ?>
</div>
<?php include_once $rootPath . 'assest/config/urlBase.php'; ?>
<script>
<?php if ($result) { ?>
Swal.fire({icon:'success',title:'Importación exitosa',text:'Inspecciones creadas: <?php echo (int)($result['imported'] ?? 0); ?>'});
<?php } ?>
<?php if ($error) { ?>
Swal.fire({icon:'error',title:'Importación fallida',text:<?php echo json_encode($error); ?>});
<?php } ?>
</script>
</body>
</html>
