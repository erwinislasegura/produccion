<?php
$rootPath = __DIR__ . "/../../";
include_once $rootPath . "assest/config/validarUsuarioFruta.php";
include_once $rootPath . "assest/controlador/productor_controller.php";

$ID_EMPRESA = $_SESSION['ID_EMPRESA'];
$productorController = new ProductorController();
$productores = $productorController->index($ID_EMPRESA);
$totalProductores = is_array($productores) ? count($productores) : 0;
$totalDocumentos = 0;
$productoresConDocumento = 0;
if (!empty($productores)) {
    foreach ($productores as $productor) {
        $cantidad = (int)($productor->NUMERO_DOCUMENTOS ?? 0);
        $totalDocumentos += $cantidad;
        if ($cantidad > 0) {
            $productoresConDocumento++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Calidad - Documentos de Productores</title>
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
        .doc-kpi-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; margin-bottom:12px; }
        .doc-kpi { border:1px solid #dce5ef; border-top:3px solid #5a67d8; border-radius:10px; background:#fff; padding:10px; }
        .doc-kpi--green { border-top-color:#43a95c; }
        .doc-kpi--orange { border-top-color:#ef9b26; }
        .doc-kpi__label { font-size:11px; text-transform:uppercase; letter-spacing:.6px; color:#6f8197; font-weight:600; }
        .doc-kpi__value { font-size:24px; line-height:1; color:#24384f; font-weight:600; margin-top:5px; }
        .doc-table { width:100%; }
        .doc-table thead th { background:#f4f7fc; color:#4d5d79 !important; border-bottom:1px solid #e2e9f5; font-size:12px; text-transform:uppercase; letter-spacing:.03em; vertical-align:middle; }
        .doc-table tbody td { color:#22314a !important; border-top:1px solid #e8edf6; vertical-align:middle; }
        .doc-btn { border-radius:999px !important; font-weight:600; letter-spacing:.1px; padding:7px 14px; box-shadow:0 1px 3px rgba(31,53,90,.08); display:inline-flex; align-items:center; justify-content:center; gap:6px; }
        .doc-btn:hover { transform:translateY(-1px); box-shadow:0 3px 8px rgba(31,53,90,.12); }
        .doc-action { min-width:165px; }
        @media (max-width: 767px) { .doc-kpi-grid { grid-template-columns:1fr; } .doc-btn { width:100%; margin-top:4px; } }
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
                            <h3 class="page-title">Dashboard · Documentos de Productores</h3>
                        </div>
                        <?php include_once $rootPath . "assest/config/verIndicadorEconomico.php"; ?>
                    </div>
                </div>
                <section class="content">
                    <div class="doc-kpi-grid">
                        <div class="doc-kpi"><div class="doc-kpi__label">Productores</div><div class="doc-kpi__value"><?php echo $totalProductores; ?></div></div>
                        <div class="doc-kpi doc-kpi--green"><div class="doc-kpi__label">Con documentos</div><div class="doc-kpi__value"><?php echo $productoresConDocumento; ?></div></div>
                        <div class="doc-kpi doc-kpi--orange"><div class="doc-kpi__label">Documentos cargados</div><div class="doc-kpi__value"><?php echo $totalDocumentos; ?></div></div>
                    </div>
                    <div class="box doc-panel">
                        <div class="box-header with-border">
                            <div class="doc-toolbar">
                                <h4 class="box-title">Listado de productores</h4>
                                <a href="addDocumentoProductor.php" class="btn btn-success doc-btn"><i class="ti-plus"></i> Agregar documento</a>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="tabla-productor" class="table table-hover doc-table">
                                    <thead>
                                        <tr>
                                            <th>Acciones</th>
                                            <th>ID</th>
                                            <th>Código</th>
                                            <th>RUN</th>
                                            <th>Nombre</th>
                                            <th>Dirección</th>
                                            <th>Email</th>
                                            <th>Teléfono</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($productores)): ?>
                                            <?php foreach ($productores as $productor): ?>
                                                <?php $cantidadDocs = (int)($productor->NUMERO_DOCUMENTOS ?? 0); ?>
                                                <tr>
                                                    <td>
                                                        <a href="listaDocumento.php?id=<?php echo base64_encode($productor->ID_PRODUCTOR); ?>" class="btn btn-<?php echo $cantidadDocs > 0 ? 'info' : 'secondary'; ?> doc-btn doc-action">
                                                            <i class="ti-folder"></i> Documentación (<?php echo htmlspecialchars($cantidadDocs); ?>)
                                                        </a>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($productor->ID_PRODUCTOR); ?></td>
                                                    <td><?php echo htmlspecialchars($productor->NUMERO_PRODUCTOR); ?></td>
                                                    <td><?php echo htmlspecialchars($productor->RUT_PRODUCTOR . '-' . $productor->DV_PRODUCTOR); ?></td>
                                                    <td><?php echo htmlspecialchars($productor->NOMBRE_PRODUCTOR); ?></td>
                                                    <td><?php echo htmlspecialchars($productor->DIRECCION_PRODUCTOR); ?></td>
                                                    <td><?php echo htmlspecialchars($productor->EMAIL_PRODUCTOR); ?></td>
                                                    <td><?php echo htmlspecialchars($productor->TELEFONO_PRODUCTOR); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="8" class="text-center text-muted">No hay productores disponibles.</td></tr>
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
        $('#tabla-productor').DataTable({
            ordering: false,
            paging: true,
            searching: true,
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": { "sFirst": "Primero", "sLast": "Último", "sNext": "Siguiente", "sPrevious": "Anterior" }
            }
        });
    </script>
</body>
</html>
