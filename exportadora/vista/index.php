<?php
$rootPath = __DIR__ . "/../../";
include_once $rootPath . "assest/config/validarUsuarioExpo.php";

//LLAMADA ARCHIVOS NECESARIOS PARA LAS OPERACIONES
include_once $rootPath . "assest/controlador/CONSULTA_ADO.php";

//INICIALIZAR CONTROLADOR
$CONSULTA_ADO = new CONSULTA_ADO;

//DEFINIR ARREGLOS CON LOS DATOS OBTENIDOS DE LAS FUNCIONES DE LOS CONTROLADORES
$query_exportacionPais = $CONSULTA_ADO->TopExportacionPorPais($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_exportacionRecibidor = $CONSULTA_ADO->TopExportacionPorRecibidor($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_cajasPorPais = $CONSULTA_ADO->CajasAprobadasPorPais($TEMPORADAS, $EMPRESAS, $PLANTAS);

$totalExportado = 0;
$totalRecibidores = 0;
$totalCajas = 0;
$maxPais = 0;
$maxRecibidor = 0;
$maxCajas = 0;

if ($query_exportacionPais) {
    foreach ($query_exportacionPais as $fila) {
        $totalExportado += $fila["TOTAL"];
        if ($fila["TOTAL"] > $maxPais) {
            $maxPais = $fila["TOTAL"];
        }
    }
}

if ($query_exportacionRecibidor) {
    foreach ($query_exportacionRecibidor as $fila) {
        $totalRecibidores += $fila["TOTAL"];
        if ($fila["TOTAL"] > $maxRecibidor) {
            $maxRecibidor = $fila["TOTAL"];
        }
    }
}

if ($query_cajasPorPais) {
    foreach ($query_cajasPorPais as $fila) {
        $totalCajas += $fila["TOTAL"];
        if ($fila["TOTAL"] > $maxCajas) {
            $maxCajas = $fila["TOTAL"];
        }
    }
}



?>


<!DOCTYPE html>
<html lang="es">

<head>
    <title>INICIO</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- LLAMADA DE LOS ARCHIVOS NECESARIOS PARA DISEÑO Y FUNCIONES BASE DE LA VISTA -->
        <?php include_once $rootPath . "assest/config/urlHead.php"; ?>
        <!-- FUNCIONES BASES -->
            <script type="text/javascript">
                //REDIRECCIONAR A LA PAGINA SELECIONADA
                function irPagina(url) {
                    location.href = "" + url;
                }
                //FUNCION PARA OBTENER HORA Y FECHA
              
            </script>

</head>
<body class="hold-transition light-skin fixed sidebar-mini theme-primary sistemRR" >
    <div class="wrapper">
        <?php include_once $rootPath . "assest/config/menuExpo.php"; ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="container-full">

                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="d-flex align-items-center">
                        <div class="mr-auto">
                            <h3 class="page-title">Inicio</h3>
                            <div class="d-inline-block align-items-center">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>                                      
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <?php include_once $rootPath . "assest/config/verIndicadorEconomico.php"; ?>
                    </div>
                </div>
                <section class="content">
                    <style>
                        body.sistemRR { background: #f4f7fb; color: #2f3b4c; }
                        .expo-dashboard { font-size: 13px; }
                        .expo-dashboard .dashboard-row { margin-bottom: 10px; }
                        .expo-dashboard .panel-card {
                            background: #fff;
                            border: 1px solid #d8e1ec;
                            border-radius: 8px;
                            box-shadow: 0 1px 4px rgba(25, 42, 65, 0.05);
                            margin-bottom: 10px;
                        }
                        .expo-dashboard .panel-card__head {
                            padding: 9px 12px;
                            border-bottom: 1px solid #dbe3ee;
                            background: #eff3f8;
                            border-radius: 8px 8px 0 0;
                            font-size: 13px;
                            font-weight: 600;
                            color: #2d4057;
                        }
                        .expo-dashboard .panel-card__body { padding: 10px 12px; }
                        .expo-dashboard .summary-grid {
                            display: grid;
                            grid-template-columns: repeat(4, minmax(0, 1fr));
                            gap: 8px;
                        }
                        .expo-dashboard .summary-tile {
                            min-height: 72px;
                            padding: 10px;
                            background: #fff;
                            border: 1px solid #dce5ef;
                            border-top: 3px solid #60758f;
                            border-radius: 9px;
                        }
                        .expo-dashboard .summary-tile--green { border-top-color: #43a95c; }
                        .expo-dashboard .summary-tile--orange { border-top-color: #ef9b26; }
                        .expo-dashboard .summary-tile--red { border-top-color: #e2525c; }
                        .expo-dashboard .summary-title {
                            font-size: 11px;
                            text-transform: uppercase;
                            letter-spacing: .5px;
                            color: #6f8197;
                            font-weight: 600;
                            margin-bottom: 6px;
                        }
                        .expo-dashboard .summary-value {
                            font-size: 24px;
                            line-height: 1;
                            color: #24384f;
                            font-weight: 600;
                        }
                        .expo-dashboard .compact-table { width: 100%; margin-bottom: 0; }
                        .expo-dashboard .compact-table th,
                        .expo-dashboard .compact-table td {
                            padding: 6px 4px;
                            font-size: 12px;
                            vertical-align: middle;
                            border-top: 1px solid #edf2f8;
                        }
                        .expo-dashboard .compact-table th { color: #5d6d82; font-weight: 600; }
                        .expo-dashboard .status-progress {
                            height: 6px;
                            min-width: 105px;
                            background: #edf2f8;
                            border-radius: 999px;
                            overflow: hidden;
                        }
                        .expo-dashboard .status-progress span { display: block; height: 100%; border-radius: 999px; }
                        .expo-dashboard .quick-link {
                            display: block;
                            padding: 8px 10px;
                            margin-bottom: 7px;
                            background: #f6f8fb;
                            border: 1px solid #dde5ef;
                            border-radius: 7px;
                            color: #2f435a;
                            font-size: 13px;
                        }
                        .expo-dashboard .quick-link:hover { background: #eef3f8; color: #1f2d3d; }
                        @media (max-width: 1199px) { .expo-dashboard .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
                        @media (max-width: 767px) { .expo-dashboard .summary-grid { grid-template-columns: 1fr; } }
                    </style>

                    <div class="expo-dashboard">
                        <div class="row dashboard-row">
                            <div class="col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head d-flex justify-content-between align-items-center">
                                        <span>Resumen exportadora</span>
                                        <small class="text-muted">Temporada activa</small>
                                    </div>
                                    <div class="panel-card__body">
                                        <div class="summary-grid">
                                            <div class="summary-tile summary-tile--green">
                                                <div class="summary-title">Kilos exportados</div>
                                                <div class="summary-value"><?php echo number_format(round($totalExportado, 0), 0, ",", "."); ?></div>
                                            </div>
                                            <div class="summary-tile summary-tile--orange">
                                                <div class="summary-title">Kilos por recibidor</div>
                                                <div class="summary-value"><?php echo number_format(round($totalRecibidores, 0), 0, ",", "."); ?></div>
                                            </div>
                                            <div class="summary-tile">
                                                <div class="summary-title">Cajas aprobadas</div>
                                                <div class="summary-value"><?php echo number_format(round($totalCajas, 0), 0, ",", "."); ?></div>
                                            </div>
                                            <div class="summary-tile summary-tile--red">
                                                <div class="summary-title">Destinos activos</div>
                                                <div class="summary-value"><?php echo $query_exportacionPais ? count($query_exportacionPais) : 0; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row dashboard-row">
                            <div class="col-xl-6 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head">Top destinos por kilos</div>
                                    <div class="panel-card__body">
                                        <table class="table compact-table">
                                            <tbody>
                                                <?php if ($query_exportacionPais) { ?>
                                                    <?php foreach ($query_exportacionPais as $fila) { ?>
                                                        <?php $porcentaje = $maxPais > 0 ? ($fila["TOTAL"] / $maxPais) * 100 : 0; ?>
                                                        <tr>
                                                            <th><?php echo $fila["NOMBRE"]; ?></th>
                                                            <td class="text-right"><?php echo number_format(round($fila["TOTAL"], 0), 0, ",", "."); ?> kg</td>
                                                            <td style="width: 120px;"><div class="status-progress"><span style="width: <?php echo $porcentaje; ?>%; background:#43a95c;"></span></div></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr><td class="text-center text-muted">Sin información de destinos.</td></tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head">Top recibidores</div>
                                    <div class="panel-card__body">
                                        <table class="table compact-table">
                                            <tbody>
                                                <?php if ($query_exportacionRecibidor) { ?>
                                                    <?php foreach ($query_exportacionRecibidor as $fila) { ?>
                                                        <?php $porcentaje = $maxRecibidor > 0 ? ($fila["TOTAL"] / $maxRecibidor) * 100 : 0; ?>
                                                        <tr>
                                                            <th><?php echo $fila["NOMBRE"]; ?></th>
                                                            <td class="text-right"><?php echo number_format(round($fila["TOTAL"], 0), 0, ",", "."); ?> kg</td>
                                                            <td style="width: 120px;"><div class="status-progress"><span style="width: <?php echo $porcentaje; ?>%; background:#ef9b26;"></span></div></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr><td class="text-center text-muted">Sin información de recibidores.</td></tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row dashboard-row">
                            <div class="col-xl-4 col-lg-6 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head">Exportación</div>
                                    <div class="panel-card__body">
                                        <?php if ($PEEXPORTACION == "1") { ?><a class="quick-link" href="registroICarga.php">Registrar instrucción de carga</a><?php } ?>
                                        <?php if ($PEEXPORTACION == "1") { ?><a class="quick-link" href="listarICarga.php">Seguimiento instrucción de carga</a><?php } ?>
                                        <?php if ($PEEXPORTACION == "1") { ?><a class="quick-link" href="registroNotadc.php">Registrar nota D/C</a><?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head">Finanzas</div>
                                    <div class="panel-card__body">
                                        <?php if ($PELIQUIDACION == "1") { ?><a class="quick-link" href="listarValorLiquidacion.php">Valores de liquidación</a><?php } ?>
                                        <a class="quick-link" href="listarAnticipo.php">Anticipos</a>
                                        <?php if ($PEPAGO == "1") { ?><a class="quick-link" href="listarValorPago.php">Valores de pago</a><?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head">Informes</div>
                                    <div class="panel-card__body">
                                        <?php if ($PEINFORMES == "1") { ?><a class="quick-link" href="listarExiexportacion.php">Existencia exportación</a><?php } ?>
                                        <?php if ($PEINFORMES == "1") { ?><a class="quick-link" href="listarDespachoptDetallado.php">Despacho producto terminado</a><?php } ?>
                                        <?php if ($PEFRUTA == "1") { ?><a class="quick-link" href="registroOrdenProceso.php">Órdenes de proceso</a><?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row dashboard-row">
                            <div class="col-xl-8 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head">Cajas aprobadas por país</div>
                                    <div class="panel-card__body">
                                        <table class="table compact-table">
                                            <tbody>
                                                <?php if ($query_cajasPorPais) { ?>
                                                    <?php foreach ($query_cajasPorPais as $fila) { ?>
                                                        <?php $porcentaje = $maxCajas > 0 ? ($fila["TOTAL"] / $maxCajas) * 100 : 0; ?>
                                                        <tr>
                                                            <th><?php echo $fila["NOMBRE"]; ?></th>
                                                            <td class="text-right"><?php echo number_format(round($fila["TOTAL"], 0), 0, ",", "."); ?> cajas</td>
                                                            <td style="width: 160px;"><div class="status-progress"><span style="width: <?php echo $porcentaje; ?>%; background:#60758f;"></span></div></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr><td class="text-center text-muted">Sin cajas aprobadas registradas.</td></tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head">Gestión rápida</div>
                                    <div class="panel-card__body">
                                        <?php if ($PEFRUTA == "1" && $PEFCICARGA == "1") { ?><a class="quick-link" href="registroCambiarIcarga.php">Cambiar instrucción de carga</a><?php } ?>
                                        <?php if ($PEINFORMES == "1") { ?><a class="quick-link" href="listarRecepcionConsolidado.php">Consolidado recepción</a><?php } ?>
                                        <?php if ($PEINFORMES == "1") { ?><a class="quick-link" href="listarDespachoConsolidado.php">Consolidado despacho</a><?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <!-- LLAMADA ARCHIVO DEL DISEÑO DEL FOOTER Y MENU USUARIO -->
        <?php include_once $rootPath . "assest/config/footer.php"; ?>
        <?php include_once $rootPath . "assest/config/menuExtraExpo.php"; ?>
    </div>
    <?php include_once $rootPath . "assest/config/urlBase.php"; ?>
</body>

</html>
