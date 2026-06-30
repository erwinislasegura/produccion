<?php
$rootPath = __DIR__ . "/../../";
include_once $rootPath . "assest/config/validarUsuarioFruta.php";



//LLAMADA ARCHIVOS NECESARIOS PARA LAS OPERACIONES
include_once $rootPath . "assest/controlador/CONSULTA_ADO.php";


//INICIALIZAR CONTROLADOR
$CONSULTA_ADO =  NEW CONSULTA_ADO;

//INCIALIZAR VARIBALES A OCUPAR PARA LA FUNCIONALIDAD

$query_datosPlanta = $CONSULTA_ADO->verPlanta($PLANTAS);

//acumulados materia prima
$query_acumuladoMP = $CONSULTA_ADO->TotalKgMpRecepcionadoAcumulado($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_existenciaActual = $CONSULTA_ADO->TotalExistenciaMateriaPrimaActual($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_recepcionDiaActual = $CONSULTA_ADO->TotalKgMpRecepcionadoDiaActual($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_despachoDiaActual = $CONSULTA_ADO->TotalKgDespachoMpDiaActual($TEMPORADAS, $EMPRESAS, $PLANTAS);

//proceso
$query_totalesProceso = $CONSULTA_ADO->TotalKgProcesoEntradaSalida($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_totalesProcesoDiaActual = $CONSULTA_ADO->TotalKgProcesoDiaActual($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_procesosBajaExportacion = $CONSULTA_ADO->UltimosProcesosBajaExportacionCerrados($TEMPORADAS, $EMPRESAS, $PLANTAS);

//exportación
$query_exportacionProductor = $CONSULTA_ADO->TopExportacionPorProductor($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_exportacionVariedad = $CONSULTA_ADO->TopExportacionPorVariedad($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_exportacionPais = $CONSULTA_ADO->TopExportacionPorPais($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_exportacionRecibidor = $CONSULTA_ADO->TopExportacionPorRecibidor($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_cajasPorPais = $CONSULTA_ADO->CajasAprobadasPorPais($TEMPORADAS, $EMPRESAS, $PLANTAS);

//existencia materia prima
$query_existenciaVariedad = $CONSULTA_ADO->ExistenciaMateriaPrimaPorVariedad($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_registrosAbiertos = $CONSULTA_ADO->contarRegistrosAbiertosFruta($EMPRESAS, $PLANTAS, $TEMPORADAS);

$kilosMateriaPrimaAcumulado = $query_acumuladoMP ? $query_acumuladoMP[0]["TOTAL"] : 0;
$kilosMateriaPrimaActual = $query_existenciaActual ? $query_existenciaActual[0]["TOTAL"] : 0;
$kilosRecepcionDiaActual = $query_recepcionDiaActual ? $query_recepcionDiaActual[0]["TOTAL"] : 0;
$kilosProcesoDiaActual = $query_totalesProcesoDiaActual ? $query_totalesProcesoDiaActual[0]["TOTAL"] : 0;
$kilosDespachoDiaActual = $query_despachoDiaActual ? $query_despachoDiaActual[0]["TOTAL"] : 0;
$kilosMateriaPrimaHastaCinco = $kilosMateriaPrimaActual + $kilosProcesoDiaActual + $kilosDespachoDiaActual - $kilosRecepcionDiaActual;
$kilosEntradaProceso = ($query_totalesProceso && isset($query_totalesProceso[0]["ENTRADA"])) ? $query_totalesProceso[0]["ENTRADA"] : 0;
$kilosSalidaProceso = ($query_totalesProceso && isset($query_totalesProceso[0]["SALIDA"])) ? $query_totalesProceso[0]["SALIDA"] : 0;
$recepcionesAbiertas = $query_registrosAbiertos ? $query_registrosAbiertos[0]["RECEPCION"] : 0;
$procesosAbiertos = $query_registrosAbiertos ? $query_registrosAbiertos[0]["PROCESO"] : 0;
$maxExportProd = 0;
$maxExportVariedad = 0;
$maxExportPais = 0;
$maxExportRecibidor = 0;
$maxExistencia = 0;
$maxCajasPais = 0;
$totalExportProd = 0;
$totalExportVariedad = 0;
$totalExportPais = 0;
$totalExportRecibidor = 0;
$totalExistencia = 0;
$totalCajasAprobadas = 0;
$totalEntradaProcesosCerrados = 0;
$totalExportProcesosCerrados = 0;
$procesosTrend = array();

if ($query_exportacionProductor) {
    foreach ($query_exportacionProductor as $fila) {
        if ($fila["TOTAL"] > $maxExportProd) {
            $maxExportProd = $fila["TOTAL"];
        }
        $totalExportProd += $fila["TOTAL"];
    }
}
if ($query_exportacionVariedad) {
    foreach ($query_exportacionVariedad as $fila) {
        if ($fila["TOTAL"] > $maxExportVariedad) {
            $maxExportVariedad = $fila["TOTAL"];
        }
        $totalExportVariedad += $fila["TOTAL"];
    }
}
if ($query_existenciaVariedad) {
    foreach ($query_existenciaVariedad as $fila) {
        if ($fila["TOTAL"] > $maxExistencia) {
            $maxExistencia = $fila["TOTAL"];
        }
        $totalExistencia += $fila["TOTAL"];
    }
}
if ($query_exportacionPais) {
    foreach ($query_exportacionPais as $fila) {
        if ($fila["TOTAL"] > $maxExportPais) {
            $maxExportPais = $fila["TOTAL"];
        }
        $totalExportPais += $fila["TOTAL"];
    }
}
if ($query_exportacionRecibidor) {
    foreach ($query_exportacionRecibidor as $fila) {
        if ($fila["TOTAL"] > $maxExportRecibidor) {
            $maxExportRecibidor = $fila["TOTAL"];
        }
        $totalExportRecibidor += $fila["TOTAL"];
    }
}

if ($query_cajasPorPais) {
    foreach ($query_cajasPorPais as $fila) {
        if ($fila["TOTAL"] > $maxCajasPais) {
            $maxCajasPais = $fila["TOTAL"];
        }
        $totalCajasAprobadas += $fila["TOTAL"];
    }
}

if ($query_procesosBajaExportacion) {
    foreach ($query_procesosBajaExportacion as $procesoTotal) {
        $totalEntradaProcesosCerrados += $procesoTotal["KILOS_NETO_ENTRADA"];
        $totalExportProcesosCerrados += $procesoTotal["KILOS_EXPORTACION_PROCESO"];
    }
    $procesosTrend = array_slice($query_procesosBajaExportacion, 0, 5);
}

if ($query_datosPlanta) {
    $nombePlanta = $query_datosPlanta[0]['NOMBRE_PLANTA'];
}

$totalRegistrosEstado = $recepcionesAbiertas + $procesosAbiertos;
$porcRecepciones = $totalRegistrosEstado > 0 ? ($recepcionesAbiertas / $totalRegistrosEstado) * 100 : 0;
$porcProcesos = $totalRegistrosEstado > 0 ? ($procesosAbiertos / $totalRegistrosEstado) * 100 : 0;
$porcCerrados = $totalRegistrosEstado > 0 ? 100 - ($porcRecepciones + $porcProcesos) : 0;

$stockCritico = ($kilosMateriaPrimaActual <= 0) ? 1 : 0;
$stockBajo = ($kilosMateriaPrimaActual > 0 && $kilosMateriaPrimaActual < 10000) ? 1 : 0;
$stockNormal = ($kilosMateriaPrimaActual >= 10000) ? 1 : 0;






/*$RECEPCION=0;
$RECEPCIONMP=0;
$RECEPCIONIND=0;
$RECEPCIONPT=0;
$DESPACHO=0;
$PROCESO=0;
$REEMBALAJE=0;
$REPALETIZAJE=0;

//INICIALIZAR ARREGLOS
$ARRAYREGISTROSABIERTOS="";
$ARRAYAVISOS1=$AVISO_ADO->listarAvisoActivosCBX();
//$ARRAYAVISOS2=$AVISO_ADO->listarAvisoActivosFijoCBX();



//DEFINIR ARREGLOS CON LOS DATOS OBTENIDOS DE LAS FUNCIONES DE LOS CONTROLADORES
$ARRAYREGISTROSABIERTOS=$CONSULTA_ADO->contarRegistrosAbiertosFruta($EMPRESAS,$PLANTAS,$TEMPORADAS);
if($ARRAYREGISTROSABIERTOS){
    $RECEPCION=$ARRAYREGISTROSABIERTOS[0]["RECEPCION"];
    $RECEPCIONMP=$ARRAYREGISTROSABIERTOS[0]["RECEPCIONMP"];
    $RECEPCIONIND=$ARRAYREGISTROSABIERTOS[0]["RECEPCIONIND"];
    $RECEPCIONPT=$ARRAYREGISTROSABIERTOS[0]["RECEPCIONPT"];
    $DESPACHO=$ARRAYREGISTROSABIERTOS[0]["DESPACHO"];
    $DESPACHOMP=$ARRAYREGISTROSABIERTOS[0]["DESPACHOMP"];
    $DESPACHOIND=$ARRAYREGISTROSABIERTOS[0]["DESPACHOIND"];
    $DESPACHOPT=$ARRAYREGISTROSABIERTOS[0]["DESPACHOPT"];
    $DESPACHOEXPO=$ARRAYREGISTROSABIERTOS[0]["DESPACHOEXPO"];
    $PROCESO=$ARRAYREGISTROSABIERTOS[0]["PROCESO"];
    $REEMBALAJE=$ARRAYREGISTROSABIERTOS[0]["REEMBALAJE"];
    $REPALETIZAJE=$ARRAYREGISTROSABIERTOS[0]["REPALETIZAJE"];
}*/


?>


<!DOCTYPE html>
<html lang="es">
<head>
    <title>INICIO</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="">
    <meta name="author" content="">
        <!- LLAMADA DE LOS ARCHIVOS NECESARIOS PARA DISEÑO Y FUNCIONES BASE DE LA VISTA -!>
        <?php include_once "../../assest/config/urlHead.php"; ?>
        <style>
            body.sistemRR {
                font-family: "Inter", "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                font-weight: 400;
                color: #2f3b4c;
                letter-spacing: .1px;
                background: #f4f7fb;
            }
            section.content {
                padding-top: 8px;
            }
            .content-header {
                margin-bottom: 4px;
                padding: 2px 0 0;
            }
            .content-header .page-title {
                font-size: 16px;
                font-weight: 600;
                letter-spacing: .1px;
                margin-bottom: 0;
                color: #1f2d3d;
            }
            .dashboard-row {
                margin-bottom: 10px;
            }
            .collage-row {
                margin-left: -6px;
                margin-right: -6px;
            }
            .collage-row > [class*='col-'] {
                padding-left: 6px;
                padding-right: 6px;
            }
            @media (min-width: 1200px) {
                .col-xl-5th {
                    flex: 0 0 20%;
                    max-width: 20%;
                }
            }
            .row .col-xl-5th {
                display: flex;
            }
            .dashboard-card {
                background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
                color: #1f2d3d;
                border: 1px solid #dde5ef;
                border-radius: 10px;
                box-shadow: 0 2px 8px rgba(27, 39, 59, 0.04);
                height: 100%;
            }
            .dashboard-card .box-body {
                padding: 12px 14px;
            }
            .metric-label {
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: .6px;
                color: #7f8da1;
                margin-bottom: 4px;
                font-weight: 500;
            }
            .metric-value {
                font-size: 19px;
                font-weight: 500;
                margin: 0;
                color: #1f2d3d;
                line-height: 1.15;
            }
            .metric-icon {
                font-size: 30px;
                color: #c2ccda;
            }
            .compact-card .box-body {
                padding: 10px 12px;
            }
            .compact-card .box-header {
                padding: 9px 12px;
                border-bottom: 1px solid #e8edf4;
            }
            .compact-card .box-title {
                font-weight: 500;
                font-size: 14px;
                color: #2a384b;
            }
            .compact-table th,
            .compact-table td {
                padding: 5px 4px;
                font-size: 11px;
                vertical-align: middle;
            }
            .compact-table th {
                font-weight: 500;
                color: #5d6d82;
            }
            .minimal-progress {
                width: 100%;
                max-width: 120px;
                margin-left: auto;
            }
            .minimal-progress .progress {
                height: 4px;
                margin-bottom: 2px;
                background: #ebf0f6;
                border-radius: 999px;
                box-shadow: none;
            }
            .minimal-progress .progress-bar {
                background: #60758f;
            }
            .minimal-progress__text {
                display: block;
                text-align: right;
                font-size: 10px;
                color: #6b7b8f;
                line-height: 1.2;
            }
            .badge {
                font-weight: 500;
                letter-spacing: .2px;
            }
            .table-striped tbody tr:nth-of-type(odd) {
                background-color: rgba(233, 239, 247, 0.3);
            }
            .box.compact-card {
                border: 1px solid #dde6f0;
                border-radius: 10px;
                box-shadow: 0 2px 8px rgba(17, 32, 51, 0.04);
            }
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
            .panel-card__body {
                padding: 10px 12px;
            }
            .trend-mock {
                height: 255px;
                border: 1px solid #e2e8f0;
                border-radius: 6px;
                background: linear-gradient(to bottom, #fafcff 0, #fafcff 49%, #f0f4fa 50%, #f0f4fa 100%);
                padding: 12px;
                display: flex;
                align-items: flex-end;
                gap: 8px;
            }
            .trend-bar {
                flex: 1;
                min-width: 56px;
            }
            .trend-bar__fill {
                border: 1px solid #7258d8;
                background: rgba(114, 88, 216, 0.35);
                border-radius: 4px 4px 0 0;
            }
            .trend-bar__label {
                font-size: 10px;
                text-align: center;
                color: #657a92;
                margin-top: 4px;
            }
            .status-row {
                display: flex;
                justify-content: space-between;
                font-size: 13px;
                margin-bottom: 4px;
            }
            .status-progress {
                height: 7px;
                background: #edf2f8;
                border-radius: 999px;
                margin-bottom: 8px;
            }
            .status-progress > span {
                display: block;
                height: 100%;
                border-radius: 999px;
            }
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
            .kpi-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 8px;
            }
            .kpi-tile {
                border: 1px solid #dce5ef;
                border-top: 3px solid #5a67d8;
                border-radius: 10px;
                background: #fff;
                padding: 10px;
                min-height: 78px;
            }
            .kpi-title {
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: .6px;
                color: #6f8197;
                margin-bottom: 5px;
                font-weight: 600;
            }
            .kpi-value {
                font-size: 29px;
                line-height: 1;
                color: #24384f;
                font-weight: 600;
            }
            .top-green { border-top-color: #43a95c; }
            .top-red { border-top-color: #e2525c; }
            .top-orange { border-top-color: #ef9b26; }
            @media (max-width: 1199px) {
                .kpi-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }
            @media (max-width: 767px) {
                .kpi-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
        <!- FUNCIONES BASES -!>
        <script type="text/javascript">
            //REDIRECCIONAR A LA PAGINA SELECIONADA
            function irPagina(url) {
                location.href = "" + url;
            }
        </script>
</head>
<body class="hold-transition light-skin fixed sidebar-mini theme-primary sistemRR" >
    <div class="wrapper">
        <!- LLAMADA AL MENU PRINCIPAL DE LA PAGINA-!>
            <?php include_once "../../assest/config/menuFruta.php"; ?>
            <!- LLAMADA ARCHIVO DEL DISEÑO DEL FOOTER Y MENU USUARIO -!>
            <div class="content-wrapper">
                <div class="container-full">
                    <section class="content">
                        <div class="content-header">
                            <div class="d-flex align-items-center">
                                <div class="mr-auto">
                                    <h3 class="page-title mb-0">Dashboard · Planta <?php echo isset($nombePlanta) ? strtoupper($nombePlanta) : ""; ?></h3>
                                </div>
                                <?php include_once "../../assest/config/verIndicadorEconomico.php"; ?>
                            </div>
                        </div>

                        <div class="row dashboard-row">
                            <div class="col-xl-8 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head d-flex justify-content-between align-items-center">
                                        <span>Tendencia operativa (últimos procesos cerrados)</span>
                                        <small class="text-muted">Entrada vs exportación</small>
                                    </div>
                                    <div class="panel-card__body">
                                        <?php if ($procesosTrend) { ?>
                                            <div class="trend-mock">
                                                <?php
                                                $maxTrendEntrada = 0;
                                                foreach ($procesosTrend as $trend) {
                                                    if ($trend["KILOS_NETO_ENTRADA"] > $maxTrendEntrada) {
                                                        $maxTrendEntrada = $trend["KILOS_NETO_ENTRADA"];
                                                    }
                                                }
                                                foreach ($procesosTrend as $trend) {
                                                    $altura = $maxTrendEntrada > 0 ? ($trend["KILOS_NETO_ENTRADA"] / $maxTrendEntrada) * 100 : 0;
                                                ?>
                                                    <div class="trend-bar">
                                                        <div class="trend-bar__fill" style="height: <?php echo $altura; ?>%"></div>
                                                        <div class="trend-bar__label">#<?php echo $trend["NUMERO_PROCESO"]; ?></div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="text-muted small mt-2">
                                                Referencia visual de kilos netos de entrada por proceso cerrado.
                                            </div>
                                        <?php } else { ?>
                                            <p class="mb-0 text-center">Sin datos para tendencia operativa.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head">Objetivos por estado</div>
                                    <div class="panel-card__body">
                                        <div class="status-row"><span>Recepciones abiertas</span><strong><?php echo number_format($porcRecepciones, 0, ",", "."); ?>%</strong></div>
                                        <div class="status-progress"><span style="width: <?php echo $porcRecepciones; ?>%; background:#43a95c;"></span></div>
                                        <div class="status-row"><span>Procesos abiertos</span><strong><?php echo number_format($porcProcesos, 0, ",", "."); ?>%</strong></div>
                                        <div class="status-progress"><span style="width: <?php echo $porcProcesos; ?>%; background:#e0a13a;"></span></div>
                                        <div class="status-row"><span>Estado restante</span><strong><?php echo number_format($porcCerrados, 0, ",", "."); ?>%</strong></div>
                                        <div class="status-progress"><span style="width: <?php echo $porcCerrados; ?>%; background:#e2525c;"></span></div>
                                    </div>
                                </div>
                                <div class="panel-card">
                                    <div class="panel-card__head">Semáforo operativo</div>
                                    <div class="panel-card__body">
                                        <div class="d-flex justify-content-between mb-1"><span>Stock crítico</span><strong class="text-danger"><?php echo $stockCritico; ?></strong></div>
                                        <div class="d-flex justify-content-between mb-1"><span>Stock bajo</span><strong><?php echo $stockBajo; ?></strong></div>
                                        <div class="d-flex justify-content-between mb-1"><span>Stock normal</span><strong><?php echo $stockNormal; ?></strong></div>
                                        <div class="d-flex justify-content-between mb-1"><span>Recepciones abiertas</span><strong><?php echo intval($recepcionesAbiertas); ?></strong></div>
                                        <div class="d-flex justify-content-between"><span>Procesos abiertos</span><strong><?php echo intval($procesosAbiertos); ?></strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row dashboard-row">
                            <div class="col-xl-4 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head">Atajos del flujo diario</div>
                                    <div class="panel-card__body">
                                        <a class="quick-link" href="registroDrecepcionmp.php"><i class="fa fa-truck mr-1"></i> Registrar recepción MP</a>
                                        <a class="quick-link" href="registroProceso.php"><i class="fa fa-cogs mr-1"></i> Registrar proceso</a>
                                        <a class="quick-link" href="registroSelecionExistenciaMPDespachoMp.php"><i class="fa fa-share-square mr-1"></i> Registrar despacho MP</a>
                                        <a class="quick-link" href="listarProceso.php"><i class="fa fa-search mr-1"></i> Seguimiento de procesos</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-8 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head">KPIs del módulo fruta</div>
                                    <div class="panel-card__body">
                                        <div class="kpi-grid">
                                            <div class="kpi-tile">
                                                <div class="kpi-title">Recepciones abiertas</div>
                                                <div class="kpi-value"><?php echo intval($recepcionesAbiertas); ?></div>
                                            </div>
                                            <div class="kpi-tile top-green">
                                                <div class="kpi-title">Procesos abiertos</div>
                                                <div class="kpi-value"><?php echo intval($procesosAbiertos); ?></div>
                                            </div>
                                            <div class="kpi-tile top-red">
                                                <div class="kpi-title">Stock crítico</div>
                                                <div class="kpi-value"><?php echo $stockCritico; ?></div>
                                            </div>
                                            <div class="kpi-tile">
                                                <div class="kpi-title">Stock normal</div>
                                                <div class="kpi-value"><?php echo $stockNormal; ?></div>
                                            </div>
                                            <div class="kpi-tile top-green">
                                                <div class="kpi-title">Kilos recepcionados hoy</div>
                                                <div class="kpi-value" style="font-size:22px;"><?php echo number_format(round($kilosRecepcionDiaActual, 0), 0, ",", "."); ?></div>
                                            </div>
                                            <div class="kpi-tile top-orange">
                                                <div class="kpi-title">Kilos en proceso hoy</div>
                                                <div class="kpi-value" style="font-size:22px;"><?php echo number_format(round($kilosProcesoDiaActual, 0), 0, ",", "."); ?></div>
                                            </div>
                                            <div class="kpi-tile top-red">
                                                <div class="kpi-title">Kilos despacho hoy</div>
                                                <div class="kpi-value" style="font-size:22px;"><?php echo number_format(round($kilosDespachoDiaActual, 0), 0, ",", "."); ?></div>
                                            </div>
                                            <div class="kpi-tile top-orange">
                                                <div class="kpi-title">Existencia actual (kg)</div>
                                                <div class="kpi-value" style="font-size:22px;"><?php echo number_format(round($kilosMateriaPrimaActual, 0), 0, ",", "."); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row dashboard-row">
                            <div class="col-xl-5th col-lg-6 col-12">
                                <div class="box dashboard-card">
                                    <div class="box-body">
                                        <div class="flexbox align-items-center">
                                            <div>
                                                <div class="metric-label">Kilos netos materia prima acumulados</div>
                                                <p class="metric-value"><?php echo number_format(round($kilosMateriaPrimaAcumulado, 0), 0, ",", "."); ?> kg</p>
                                            </div>
                                            <span class="icon-Add-cart metric-icon"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-5th col-lg-6 col-12">
                                <div class="box dashboard-card">
                                    <div class="box-body">
                                        <div class="flexbox align-items-center">
                                            <div>
                                                <div class="metric-label">Existencia neta (corte 05:00)</div>
                                                <p class="metric-value"><?php echo number_format(round($kilosMateriaPrimaHastaCinco, 0), 0, ",", "."); ?> kg</p>
                                            </div>
                                            <span class="icon-Alarm-clock metric-icon"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-5th col-lg-6 col-12">
                                <div class="box dashboard-card">
                                    <div class="box-body">
                                        <div class="flexbox align-items-center">
                                            <div>
                                                <div class="metric-label">Existencia neta en tiempo real</div>
                                                <p class="metric-value"><?php echo number_format(round($kilosMateriaPrimaActual, 0), 0, ",", "."); ?> kg</p>
                                            </div>
                                            <span class="icon-Network metric-icon"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-5th col-lg-6 col-12">
                                <div class="box dashboard-card">
                                    <div class="box-body">
                                        <div class="flexbox align-items-center">
                                            <div>
                                                <div class="metric-label">Proceso - kilos netos entrada</div>
                                                <p class="metric-value"><?php echo number_format(round($kilosEntradaProceso, 0), 0, ",", "."); ?> kg</p>
                                            </div>
                                            <span class="icon-Incoming-mail metric-icon"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-5th col-lg-6 col-12">
                                <div class="box dashboard-card">
                                    <div class="box-body">
                                        <div class="flexbox align-items-center">
                                            <div>
                                                <div class="metric-label">Proceso - kilos netos salida</div>
                                                <p class="metric-value"><?php echo number_format(round($kilosSalidaProceso, 0), 0, ",", "."); ?> kg</p>
                                            </div>
                                            <span class="icon-Outcoming-mail metric-icon"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row dashboard-row collage-row align-items-stretch">
                            <div class="col-xl-4 col-12">
                                <div class="box compact-card collage-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Indicadores operacionales</h4>
                                            <span class="badge badge-outline badge-info">Procesos / recepción</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="badge badge-pill badge-info mr-2"><i class="icon-Notes"></i></span>
                                            <div>
                                                <div class="text-muted small">Recepciones abiertas</div>
                                                <div class="h5 mb-0"><?php echo intval($recepcionesAbiertas); ?></div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="badge badge-pill badge-success mr-2"><i class="icon-Gear"></i></span>
                                            <div>
                                                <div class="text-muted small">Procesos abiertos</div>
                                                <div class="h5 mb-0"><?php echo intval($procesosAbiertos); ?></div>
                                            </div>
                                        </div>
                                        <div class="bg-light p-2 rounded mb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted small">Existencia neta corte 05:00</span>
                                                <span class="badge badge-primary"><?php echo number_format(round($kilosMateriaPrimaHastaCinco, 0), 0, ",", "."); ?> kg</span>
                                            </div>
                                        </div>
                                        <div class="bg-light p-2 rounded">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted small">Existencia neta al momento</span>
                                                <span class="badge badge-info"><?php echo number_format(round($kilosMateriaPrimaActual, 0), 0, ",", "."); ?> kg</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-12">
                                <div class="box compact-card collage-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Existencia de materia prima por variedad</h4>
                                            <span class="badge badge-outline badge-success">Materia prima</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_existenciaVariedad) { ?>
                                            <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                                <span>Total variedades</span>
                                                <span class="badge badge-secondary"><?php echo number_format(round($totalExistencia, 0), 0, ",", "."); ?> kg</span>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0 compact-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Variedad</th>
                                                            <th class="text-right">Kg netos</th>
                                                            <th class="text-right">Avance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($query_existenciaVariedad as $fila) {
                                                            $nombreExi = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin nombre";
                                                            $totalExi = round($fila["TOTAL"], 0);
                                                            $porcentajeExi = $maxExistencia > 0 ? ($totalExi / $maxExistencia) * 100 : 0;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $nombreExi; ?></td>
                                                                <td class="text-right"><?php echo number_format($totalExi, 0, ",", "."); ?> kg</td>
                                                                <td class="text-right">
                                                                    <div class="minimal-progress">
                                                                        <div class="progress">
                                                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $porcentajeExi; ?>%" aria-valuenow="<?php echo $porcentajeExi; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                        </div>
                                                                        <span class="minimal-progress__text"><?php echo number_format($porcentajeExi, 2, ",", "."); ?>%</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">No hay existencias registradas.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-12">
                                <div class="box compact-card collage-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Top 5 exportación por productor</h4>
                                            <span class="badge badge-outline badge-warning">Proceso</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_exportacionProductor) { ?>
                                            <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                                <span>Total top 5 productores</span>
                                                <span class="badge badge-secondary"><?php echo number_format(round($totalExportProd, 0), 0, ",", "."); ?> kg</span>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0 compact-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Productor</th>
                                                            <th class="text-right">Kg netos</th>
                                                            <th class="text-right">Avance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($query_exportacionProductor as $fila) {
                                                            $nombreProd = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin nombre";
                                                            $totalProd = round($fila["TOTAL"], 0);
                                                            $porcentajeProd = $maxExportProd > 0 ? ($totalProd / $maxExportProd) * 100 : 0;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $nombreProd; ?></td>
                                                                <td class="text-right"><?php echo number_format($totalProd, 0, ",", "."); ?> kg</td>
                                                                <td class="text-right">
                                                                    <div class="minimal-progress">
                                                                        <div class="progress">
                                                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $porcentajeProd; ?>%" aria-valuenow="<?php echo $porcentajeProd; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                        </div>
                                                                        <span class="minimal-progress__text"><?php echo number_format($porcentajeProd, 2, ",", "."); ?>%</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <p class="text-muted small mt-1 mb-0">Totales consideran únicamente los registros mostrados (top 5). Origen: procesos.</p>
                                            </div>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin exportaciones registradas.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row dashboard-row collage-row align-items-stretch">
                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="box compact-card collage-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Top 5 exportación por variedad</h4>
                                            <span class="badge badge-outline badge-warning">Proceso</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_exportacionVariedad) { ?>
                                            <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                                <span>Total top 5 variedades</span>
                                                <span class="badge badge-secondary"><?php echo number_format(round($totalExportVariedad, 0), 0, ",", "."); ?> kg</span>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0 compact-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Variedad</th>
                                                            <th class="text-right">Kg netos</th>
                                                            <th class="text-right">Avance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($query_exportacionVariedad as $fila) {
                                                            $nombreVar = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin nombre";
                                                            $totalVar = round($fila["TOTAL"], 0);
                                                            $porcentajeVar = $maxExportVariedad > 0 ? ($totalVar / $maxExportVariedad) * 100 : 0;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $nombreVar; ?></td>
                                                                <td class="text-right"><?php echo number_format($totalVar, 0, ",", "."); ?> kg</td>
                                                                <td class="text-right">
                                                                    <div class="minimal-progress">
                                                                        <div class="progress">
                                                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $porcentajeVar; ?>%" aria-valuenow="<?php echo $porcentajeVar; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                        </div>
                                                                        <span class="minimal-progress__text"><?php echo number_format($porcentajeVar, 2, ",", "."); ?>%</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <p class="text-muted small mt-1 mb-0">Totales consideran únicamente los registros mostrados (top 5). Origen: procesos.</p>
                                            </div>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin exportaciones registradas.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="box compact-card collage-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Kg netos exportados por país</h4>
                                            <span class="badge badge-outline badge-primary">Exportación</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_exportacionPais) { ?>
                                            <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                                <span>Total top 5 países</span>
                                                <span class="badge badge-secondary"><?php echo number_format(round($totalExportPais, 0), 0, ",", "."); ?> kg</span>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0 compact-table">
                                                    <thead>
                                                        <tr>
                                                            <th>País</th>
                                                            <th class="text-right">Kg netos</th>
                                                            <th class="text-right">Avance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($query_exportacionPais as $fila) {
                                                            $nombrePais = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin país";
                                                            $totalPais = round($fila["TOTAL"], 0);
                                                            $porcentajePais = $maxExportPais > 0 ? ($totalPais / $maxExportPais) * 100 : 0;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $nombrePais; ?></td>
                                                                <td class="text-right"><?php echo number_format($totalPais, 0, ",", "."); ?> kg</td>
                                                                <td class="text-right">
                                                                    <div class="minimal-progress">
                                                                        <div class="progress">
                                                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $porcentajePais; ?>%" aria-valuenow="<?php echo $porcentajePais; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                        </div>
                                                                        <span class="minimal-progress__text"><?php echo number_format($porcentajePais, 2, ",", "."); ?>%</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <p class="text-muted small mt-1 mb-0">Totales consideran únicamente los registros mostrados (top 5).</p>
                                            </div>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin destinos registrados.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="box compact-card collage-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Kg netos exportados por recibidor</h4>
                                            <span class="badge badge-outline badge-primary">Exportación</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_exportacionRecibidor) { ?>
                                            <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                                <span>Total top 5 recibidores</span>
                                                <span class="badge badge-secondary"><?php echo number_format(round($totalExportRecibidor, 0), 0, ",", "."); ?> kg</span>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0 compact-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Recibidor</th>
                                                            <th class="text-right">Kg netos</th>
                                                            <th class="text-right">Avance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($query_exportacionRecibidor as $fila) {
                                                            $nombreRecibidor = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin recibidor";
                                                            $totalRecibidor = round($fila["TOTAL"], 0);
                                                            $porcentajeRecibidor = $maxExportRecibidor > 0 ? ($totalRecibidor / $maxExportRecibidor) * 100 : 0;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $nombreRecibidor; ?></td>
                                                                <td class="text-right"><?php echo number_format($totalRecibidor, 0, ",", "."); ?> kg</td>
                                                                <td class="text-right">
                                                                    <div class="minimal-progress">
                                                                        <div class="progress">
                                                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $porcentajeRecibidor; ?>%" aria-valuenow="<?php echo $porcentajeRecibidor; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                        </div>
                                                                        <span class="minimal-progress__text"><?php echo number_format($porcentajeRecibidor, 2, ",", "."); ?>%</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <p class="text-muted small mt-1 mb-0">Totales consideran únicamente los registros mostrados (top 5).</p>
                                            </div>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin recibidores registrados.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="box compact-card collage-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Cajas aprobadas por país</h4>
                                            <span class="badge badge-outline badge-info">Inspección</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_cajasPorPais) { ?>
                                            <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                                <span>Total top 5 países</span>
                                                <span class="badge badge-secondary"><?php echo number_format(round($totalCajasAprobadas, 0), 0, ",", "."); ?> cajas</span>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0 compact-table">
                                                    <thead>
                                                        <tr>
                                                            <th>País</th>
                                                            <th class="text-right">Cajas</th>
                                                            <th class="text-right">Avance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($query_cajasPorPais as $fila) {
                                                            $nombreCajaPais = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin país";
                                                            $totalCajas = round($fila["TOTAL"], 0);
                                                            $porcentajeCajaPais = $maxCajasPais > 0 ? ($totalCajas / $maxCajasPais) * 100 : 0;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $nombreCajaPais; ?></td>
                                                                <td class="text-right"><?php echo number_format($totalCajas, 0, ",", "."); ?></td>
                                                                <td class="text-right">
                                                                    <div class="minimal-progress">
                                                                        <div class="progress">
                                                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $porcentajeCajaPais; ?>%" aria-valuenow="<?php echo $porcentajeCajaPais; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                        </div>
                                                                        <span class="minimal-progress__text"><?php echo number_format($porcentajeCajaPais, 2, ",", "."); ?>%</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <p class="text-muted small mt-1 mb-0">Totales consideran únicamente los registros mostrados (top 5).</p>
                                            </div>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin cajas aprobadas registradas para país 1.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row dashboard-row">
                            <div class="col-12">
                                <div class="box compact-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Procesos cerrados con menor % de exportación</h4>
                                            <span class="badge badge-outline badge-warning">Proceso</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_procesosBajaExportacion) { ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0 compact-table">
                                                    <thead>
                                                        <tr>
                                                            <th># / Fecha</th>
                                                            <th class="text-right">Ent. (kg)</th>
                                                            <th class="text-right">Expo (kg)</th>
                                                            <th class="text-right">Expo %</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($query_procesosBajaExportacion as $proceso) {
                                                            $porcentajeExpo = number_format($proceso["PDEXPORTACION_PROCESO"], 2, ".", "");
                                                            $porcentajeTotal = number_format($proceso["PDEXPORTACIONCD_PROCESO"], 2, ".", "");
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    <div class="font-weight-600">#<?php echo $proceso["NUMERO_PROCESO"]; ?></div>
                                                                    <div class="text-muted small"><?php echo $proceso["FECHA_PROCESO"]; ?></div>
                                                                    <div class="minimal-progress mt-1">
                                                                        <div class="progress">
                                                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $proceso["PDEXPORTACION_PROCESO"]; ?>%" aria-valuenow="<?php echo $proceso["PDEXPORTACION_PROCESO"]; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="text-right align-middle"><?php echo number_format($proceso["KILOS_NETO_ENTRADA"], 0, ",", "."); ?></td>
                                                                <td class="text-right align-middle"><?php echo number_format($proceso["KILOS_EXPORTACION_PROCESO"], 0, ",", "."); ?></td>
                                                                <td class="text-right align-middle">
                                                                    <span class="badge badge-warning-light badge-slim">Expo <?php echo $porcentajeExpo; ?>%</span>
                                                                    <div class="text-muted small">Total <?php echo $porcentajeTotal; ?>%</div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th>Total procesos listados</th>
                                                            <th class="text-right"><?php echo number_format(round($totalEntradaProcesosCerrados, 0), 0, ",", "."); ?> kg</th>
                                                            <th class="text-right"><?php echo number_format(round($totalExportProcesosCerrados, 0), 0, ",", "."); ?> kg</th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                                <p class="text-muted small mt-1 mb-0">Datos provenientes de procesos cerrados (kilos suman lo mostrado en la tabla).</p>
                                            </div>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin procesos cerrados con baja exportación.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- /.content -->
                </div>
            </div>

            <?php include_once "../../assest/config/footer.php"; ?>
            <?php include_once "../../assest/config/menuExtraFruta.php"; ?>
    </div>
    <!- LLAMADA URL DE ARCHIVOS DE DISEÑO Y JQUERY E OTROS -!>
        <?php include_once "../../assest/config/urlBase.php"; ?>
        <!--<script>
    Morris.Bar({
        element: 'graficofrigorifico',
        data: [{
            y: 'Angus',
            a: 17600,
            b: 9500
        }, {
            y: 'BBCH',
            a: 8000,
            b: 7000
        }, {
            y: 'Greenvic',
            a: 550,
            b: 4500
        }, {
            y: 'SmartBerry One',
            a: 800,
            b: 450
        }, {
            y: 'LLF',
            a: 55000,
            b: 45000
        }],
        xkey: 'y',
        ykeys: ['a', 'b'],
        labels: ['D. Exportación', 'D. Interplanta'],
        barColors:['#ff3f3f', '#0080ff'],
        hideHover: 'auto',
        gridLineColor: '#eef0f2',
        resize: true
    });
            </script>
-->
</body>
</html>
