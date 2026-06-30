<?php
$rootPath = __DIR__ . "/../../";
include_once $rootPath . "assest/config/validarUsuarioFruta.php";

$accionesCalidad = array(
    array(
        "titulo" => "Rechazo MP",
        "descripcion" => "Registrar incidencia de materia prima",
        "url" => "registroRechazomp.php",
        "icono" => "fa fa-ban",
        "color" => "top-red"
    ),
    array(
        "titulo" => "Rechazo PT",
        "descripcion" => "Registrar rechazo de producto terminado",
        "url" => "registroRechazopt.php",
        "icono" => "fa fa-times-circle",
        "color" => "top-orange"
    ),
    array(
        "titulo" => "Levantamiento MP",
        "descripcion" => "Crear acción correctiva para materia prima",
        "url" => "registroLevantamientomp.php",
        "icono" => "fa fa-clipboard",
        "color" => "top-green"
    ),
    array(
        "titulo" => "Levantamiento PT",
        "descripcion" => "Crear acción correctiva para producto terminado",
        "url" => "registroLevantamientopt.php",
        "icono" => "fa fa-tasks",
        "color" => "top-blue"
    ),
    array(
        "titulo" => "Inspección MP",
        "descripcion" => "Abrir el nuevo flujo de inspección de materia prima",
        "url" => "../inspection-mp/index.php",
        "icono" => "fa fa-check-square-o",
        "color" => "top-green"
    )
);

$reportesCalidad = array(
    array("titulo" => "Rechazos MP agrupados", "url" => "listarRechazomp.php", "icono" => "fa fa-list"),
    array("titulo" => "Rechazos PT agrupados", "url" => "listarRechazopt.php", "icono" => "fa fa-archive"),
    array("titulo" => "Levantamientos MP agrupados", "url" => "listarLevantamientomp.php", "icono" => "fa fa-check-square-o"),
    array("titulo" => "Levantamientos PT agrupados", "url" => "listarLevantamientopt.php", "icono" => "fa fa-clipboard"),
    array("titulo" => "Detalle rechazo MP", "url" => "listarRechazompDetallado.php", "icono" => "fa fa-search-plus"),
    array("titulo" => "Detalle rechazo PT", "url" => "listarRechazoptDetallado.php", "icono" => "fa fa-file-text-o"),
    array("titulo" => "Detalle levantamiento MP", "url" => "listarLevantamientompDetallado.php", "icono" => "fa fa-chart-line"),
    array("titulo" => "Detalle levantamiento PT", "url" => "listarLevantamientoptDetallado.php", "icono" => "fa fa-area-chart")
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>INICIO</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="">
    <meta name="author" content="">
    <?php include_once $rootPath . "assest/config/urlHead.php"; ?>
    <style>
        body.sistemRR {
            font-family: "Inter", "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-weight: 400;
            color: #2f3b4c;
            letter-spacing: .1px;
            background: #f4f7fb;
        }
        section.content { padding-top: 8px; }
        .content-header { margin-bottom: 4px; padding: 2px 0 0; }
        .content-header .page-title {
            font-size: 16px;
            font-weight: 600;
            letter-spacing: .1px;
            margin-bottom: 0;
            color: #1f2d3d;
        }
        .dashboard-row { margin-bottom: 10px; }
        .panel-card {
            background: #fff;
            border: 1px solid #d8e1ec;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(25, 42, 65, 0.05);
            margin-bottom: 10px;
        }
        .panel-card__head {
            padding: 9px 12px;
            border-bottom: 1px solid #dbe3ee;
            background: #eff3f8;
            border-radius: 8px 8px 0 0;
            font-size: 13px;
            font-weight: 600;
            color: #2d4057;
        }
        .panel-card__body { padding: 10px 12px; }
        .quality-hero {
            min-height: 255px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background:
                linear-gradient(135deg, rgba(72, 93, 221, .12), rgba(67, 169, 92, .10)),
                linear-gradient(to bottom, #fafcff 0, #fafcff 49%, #f0f4fa 50%, #f0f4fa 100%);
            padding: 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .quality-hero__title { color: #24384f; font-size: 24px; font-weight: 600; margin-bottom: 6px; }
        .quality-hero__text { color: #64758a; max-width: 680px; margin-bottom: 16px; }
        .quality-flow { display: flex; align-items: flex-end; gap: 8px; min-height: 118px; }
        .quality-flow__bar { flex: 1; min-width: 58px; }
        .quality-flow__fill {
            border: 1px solid #60758f;
            background: rgba(96, 117, 143, .26);
            border-radius: 4px 4px 0 0;
        }
        .quality-flow__label { font-size: 10px; text-align: center; color: #657a92; margin-top: 4px; }
        .quick-link {
            display: block;
            background: #f6f8fb;
            border: 1px solid #dde5ef;
            border-radius: 7px;
            padding: 10px;
            margin-bottom: 8px;
            color: #2f435a;
            font-size: 14px;
        }
        .quick-link:hover { color: #24384f; background: #eef3f8; }
        .kpi-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; }
        .kpi-tile {
            border: 1px solid #dce5ef;
            border-top: 3px solid #5a67d8;
            border-radius: 10px;
            background: #fff;
            padding: 10px;
            min-height: 92px;
        }
        .kpi-title { font-size: 11px; text-transform: uppercase; letter-spacing: .6px; color: #6f8197; margin-bottom: 5px; font-weight: 600; }
        .kpi-value { font-size: 17px; line-height: 1.15; color: #24384f; font-weight: 600; }
        .kpi-description { color: #75869a; font-size: 12px; margin-top: 4px; }
        .top-green { border-top-color: #43a95c; }
        .top-red { border-top-color: #e2525c; }
        .top-orange { border-top-color: #ef9b26; }
        .top-blue { border-top-color: #3f80ea; }
        .dashboard-card {
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
            color: #1f2d3d;
            border: 1px solid #dde5ef;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(27, 39, 59, 0.04);
            height: 100%;
        }
        .dashboard-card .box-body { padding: 12px 14px; }
        .metric-label { font-size: 11px; text-transform: uppercase; letter-spacing: .6px; color: #7f8da1; margin-bottom: 4px; font-weight: 500; }
        .metric-value { font-size: 19px; font-weight: 500; margin: 0; color: #1f2d3d; line-height: 1.15; }
        .metric-icon { font-size: 30px; color: #c2ccda; }
        .status-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px; }
        .status-progress { height: 7px; background: #edf2f8; border-radius: 999px; margin-bottom: 8px; }
        .status-progress > span { display: block; height: 100%; border-radius: 999px; }
        @media (max-width: 1199px) { .kpi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 767px) { .kpi-grid { grid-template-columns: 1fr; } .quality-flow { overflow-x: auto; } }
    </style>
</head>
<body class="hold-transition light-skin fixed sidebar-mini theme-primary sistemRR">
    <div class="wrapper">
        <?php include_once $rootPath . "assest/config/menuCalidad.php"; ?>

        <div class="content-wrapper">
            <div class="container-full">
                <section class="content">
                    <div class="content-header">
                        <div class="d-flex align-items-center">
                            <div class="mr-auto">
                                <h3 class="page-title mb-0">Dashboard · Calidad de la fruta</h3>
                            </div>
                            <?php include_once $rootPath . "assest/config/verIndicadorEconomico.php"; ?>
                        </div>
                    </div>

                    <div class="row dashboard-row">
                        <div class="col-xl-8 col-12">
                            <div class="panel-card">
                                <div class="panel-card__head d-flex justify-content-between align-items-center">
                                    <span>Tablero operativo de calidad</span>
                                    <small class="text-muted">Rechazos · levantamientos · reportes</small>
                                </div>
                                <div class="panel-card__body">
                                    <div class="quality-hero">
                                        <div>
                                            <h4 class="quality-hero__title">Control diario de incidencias y acciones correctivas</h4>
                                            <p class="quality-hero__text">Accede rápidamente a los registros de materia prima y producto terminado, revisa reportes agrupados y profundiza en el detalle operacional desde un panel compacto.</p>
                                        </div>
                                        <div class="quality-flow">
                                            <div class="quality-flow__bar"><div class="quality-flow__fill" style="height: 72px;"></div><div class="quality-flow__label">Detectar</div></div>
                                            <div class="quality-flow__bar"><div class="quality-flow__fill" style="height: 96px;"></div><div class="quality-flow__label">Registrar</div></div>
                                            <div class="quality-flow__bar"><div class="quality-flow__fill" style="height: 64px;"></div><div class="quality-flow__label">Levantar</div></div>
                                            <div class="quality-flow__bar"><div class="quality-flow__fill" style="height: 104px;"></div><div class="quality-flow__label">Analizar</div></div>
                                            <div class="quality-flow__bar"><div class="quality-flow__fill" style="height: 82px;"></div><div class="quality-flow__label">Cerrar</div></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-12">
                            <div class="panel-card">
                                <div class="panel-card__head">Semáforo del flujo</div>
                                <div class="panel-card__body">
                                    <div class="status-row"><span>Registro de rechazo</span><strong>100%</strong></div>
                                    <div class="status-progress"><span style="width: 100%; background:#e2525c;"></span></div>
                                    <div class="status-row"><span>Levantamiento correctivo</span><strong>75%</strong></div>
                                    <div class="status-progress"><span style="width: 75%; background:#ef9b26;"></span></div>
                                    <div class="status-row"><span>Análisis detallado</span><strong>60%</strong></div>
                                    <div class="status-progress"><span style="width: 60%; background:#3f80ea;"></span></div>
                                    <div class="status-row"><span>Seguimiento agrupado</span><strong>90%</strong></div>
                                    <div class="status-progress"><span style="width: 90%; background:#43a95c;"></span></div>
                                </div>
                            </div>
                            <div class="panel-card">
                                <div class="panel-card__head">Panel de control</div>
                                <div class="panel-card__body">
                                    <div class="d-flex justify-content-between mb-1"><span>Fecha</span><strong><?php echo date('d-m-Y'); ?></strong></div>
                                    <div class="d-flex justify-content-between mb-1"><span>Hora servidor</span><strong><?php echo date('H:i'); ?></strong></div>
                                    <div class="d-flex justify-content-between mb-1"><span>Estado</span><span class="badge badge-success">Operativo</span></div>
                                    <div class="d-flex justify-content-between"><span>Módulo</span><strong>Calidad</strong></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row dashboard-row">
                        <div class="col-xl-4 col-12">
                            <div class="panel-card">
                                <div class="panel-card__head">Atajos del flujo diario</div>
                                <div class="panel-card__body">
                                    <?php foreach ($accionesCalidad as $accion) { ?>
                                        <a class="quick-link" href="<?php echo $accion['url']; ?>"><i class="<?php echo $accion['icono']; ?> mr-1"></i> <?php echo $accion['titulo']; ?></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8 col-12">
                            <div class="panel-card">
                                <div class="panel-card__head">Acciones principales del módulo calidad</div>
                                <div class="panel-card__body">
                                    <div class="kpi-grid">
                                        <?php foreach ($accionesCalidad as $accion) { ?>
                                            <a class="kpi-tile <?php echo $accion['color']; ?>" href="<?php echo $accion['url']; ?>">
                                                <div class="kpi-title"><i class="<?php echo $accion['icono']; ?> mr-1"></i><?php echo $accion['titulo']; ?></div>
                                                <div class="kpi-value">Abrir formulario</div>
                                                <div class="kpi-description"><?php echo $accion['descripcion']; ?></div>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row dashboard-row">
                        <?php foreach ($reportesCalidad as $reporte) { ?>
                            <div class="col-xl-3 col-md-6 col-12 mb-10">
                                <div class="box dashboard-card">
                                    <div class="box-body">
                                        <div class="flexbox align-items-center">
                                            <div>
                                                <div class="metric-label">Reporte disponible</div>
                                                <a class="metric-value" href="<?php echo $reporte['url']; ?>"><?php echo $reporte['titulo']; ?></a>
                                            </div>
                                            <i class="<?php echo $reporte['icono']; ?> metric-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </section>
            </div>
        </div>

        <?php include_once $rootPath . "assest/config/footer.php"; ?>
        <?php include_once $rootPath . "assest/config/menuExtraFruta.php"; ?>
    </div>

    <?php include_once $rootPath . "assest/config/urlBase.php"; ?>
</body>
</html>
