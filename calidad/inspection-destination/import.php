<?php
include_once '../../assest/config/validarUsuarioFruta.php';
include_once '../../assest/controlador/InspectionDestinationController.php';

$controller = new InspectionDestinationController();
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
<!DOCTYPE html><html lang="es"><head><title>Importar Inspección en Destino</title><?php include_once '../../assest/config/urlHead.php'; ?></head>
<body class="hold-transition light-skin fixed sidebar-mini theme-primary sistemRR"><div class="wrapper"><?php include_once '../../assest/config/menuFruta.php'; ?>
<div class="content-wrapper"><div class="container-full"><section class="content"><div class="box"><div class="box-header with-border bg-warning"><h4 class="box-title">POST /inspection-destination/import</h4></div><form class="form-one-line" data-form-layout="oneline-1" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.65rem;padding:0.65rem 0;border-bottom:1px solid #3a3a3a;" method="post" enctype="multipart/form-data"><div class="box-body"><div class="form-group"><label>Archivo Excel</label><input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls" required></div></div><div class="box-footer"><button type="submit" class="btn btn-success">Importar</button><a href="index.php" class="btn btn-secondary">Volver</a></div></form></div></section></div></div><?php include_once '../../assest/config/footer.php'; ?><?php include_once '../../assest/config/menuExtraFruta.php'; ?></div><?php include_once '../../assest/config/urlBase.php'; ?>
<script> <?php if ($result) { ?>Swal.fire({icon:'success',title:'Importación exitosa',text:'Inspecciones creadas: <?php echo (int)($result['imported'] ?? 0); ?>'});<?php } if ($error) { ?>Swal.fire({icon:'error',title:'Importación fallida',text:<?php echo json_encode($error); ?>});<?php } ?> </script>
</body></html>
