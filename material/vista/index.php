<?php
$rootPath = __DIR__ . "/../../";
include_once $rootPath . "assest/config/validarUsuarioMaterial.php";



//LLAMADA ARCHIVOS NECESARIOS PARA LAS OPERACIONES
include_once $rootPath . "assest/controlador/CONSULTA_ADO.php";


//INICIALIZAR CONTROLADOR
$CONSULTA_ADO =  NEW CONSULTA_ADO;
//INCIALIZAR VARIBALES A OCUPAR PARA LA FUNCIONALIDAD
$RECEPCIONE=0;
$RECEPCIONM=0;
$DESPACHOE=0;
$DESPACHOM=0;


//INICIALIZAR ARREGLOS
$ARRAYREGISTROSABIERTOS="";


//DEFINIR ARREGLOS CON LOS DATOS OBTENIDOS DE LAS FUNCIONES DE LOS CONTROLADORES
$ARRAYREGISTROSABIERTOS=$CONSULTA_ADO->contarRegistrosAbiertosMateriales($EMPRESAS,$PLANTAS,$TEMPORADAS);
if($ARRAYREGISTROSABIERTOS){
    $RECEPCIONE=$ARRAYREGISTROSABIERTOS[0]["RECEPCIONE"];
    $RECEPCIONM=$ARRAYREGISTROSABIERTOS[0]["RECEPCIONM"];
    $DESPACHOE=$ARRAYREGISTROSABIERTOS[0]["DESPACHOE"];
    $DESPACHOM=$ARRAYREGISTROSABIERTOS[0]["DESPACHOM"];
}



include_once $rootPath . "assest/config/ValidardatosUrl.php";

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
        <?php include_once $rootPath . "assest/config/urlHead.php"; ?>
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
        <?php include_once $rootPath . "assest/config/menuMaterial.php"; ?>
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
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <?php include_once $rootPath . "assest/config/verIndicadorEconomico.php"; ?>
                    </div>
                </div>
                <section class="content">
                    <style>
                        body.sistemRR {
                            background: #f4f7fb;
                            color: #2f3b4c;
                        }
                        .material-dashboard {
                            font-size: 13px;
                        }
                        .material-dashboard .dashboard-row {
                            margin-bottom: 10px;
                        }
                        .material-dashboard .panel-card {
                            background: #fff;
                            border: 1px solid #d8e1ec;
                            border-radius: 8px;
                            box-shadow: 0 1px 4px rgba(25, 42, 65, 0.05);
                            margin-bottom: 10px;
                        }
                        .material-dashboard .panel-card__head {
                            padding: 9px 12px;
                            border-bottom: 1px solid #dbe3ee;
                            background: #eff3f8;
                            border-radius: 8px 8px 0 0;
                            font-size: 13px;
                            font-weight: 600;
                            color: #2d4057;
                        }
                        .material-dashboard .panel-card__body {
                            padding: 10px 12px;
                        }
                        .material-dashboard .summary-grid {
                            display: grid;
                            grid-template-columns: repeat(4, minmax(0, 1fr));
                            gap: 8px;
                        }
                        .material-dashboard .summary-tile {
                            display: block;
                            min-height: 70px;
                            padding: 10px;
                            background: #fff;
                            border: 1px solid #dce5ef;
                            border-top: 3px solid #60758f;
                            border-radius: 9px;
                            color: #24384f;
                        }
                        .material-dashboard .summary-tile:hover {
                            background: #f8fafc;
                            color: #24384f;
                        }
                        .material-dashboard .summary-tile--green { border-top-color: #43a95c; }
                        .material-dashboard .summary-tile--orange { border-top-color: #ef9b26; }
                        .material-dashboard .summary-tile--red { border-top-color: #e2525c; }
                        .material-dashboard .summary-title {
                            font-size: 11px;
                            text-transform: uppercase;
                            letter-spacing: .5px;
                            color: #6f8197;
                            font-weight: 600;
                            margin-bottom: 6px;
                        }
                        .material-dashboard .summary-value {
                            font-size: 28px;
                            line-height: 1;
                            font-weight: 600;
                        }
                        .material-dashboard .compact-table {
                            width: 100%;
                            margin-bottom: 0;
                        }
                        .material-dashboard .compact-table th,
                        .material-dashboard .compact-table td {
                            padding: 6px 4px;
                            font-size: 12px;
                            vertical-align: middle;
                            border-top: 1px solid #edf2f8;
                        }
                        .material-dashboard .compact-table th {
                            color: #5d6d82;
                            font-weight: 600;
                        }
                        .material-dashboard .status-progress {
                            height: 6px;
                            min-width: 110px;
                            background: #edf2f8;
                            border-radius: 999px;
                            overflow: hidden;
                        }
                        .material-dashboard .status-progress span {
                            display: block;
                            height: 100%;
                            border-radius: 999px;
                        }
                        .material-dashboard .quick-link {
                            display: block;
                            padding: 8px 10px;
                            margin-bottom: 7px;
                            background: #f6f8fb;
                            border: 1px solid #dde5ef;
                            border-radius: 7px;
                            color: #2f435a;
                            font-size: 13px;
                        }
                        .material-dashboard .quick-link:hover {
                            background: #eef3f8;
                            color: #1f2d3d;
                        }
                        .material-dashboard .mini-stat {
                            display: flex;
                            justify-content: space-between;
                            padding: 7px 0;
                            border-bottom: 1px solid #edf2f8;
                        }
                        .material-dashboard .mini-stat:last-child {
                            border-bottom: 0;
                        }
                        .material-dashboard .mini-stat strong {
                            color: #24384f;
                        }
                        @media (max-width: 1199px) {
                            .material-dashboard .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                        }
                        @media (max-width: 767px) {
                            .material-dashboard .summary-grid { grid-template-columns: 1fr; }
                        }
                    </style>

                    <?php
                    $TOTAL_ABIERTOS = $RECEPCIONM + $DESPACHOM + $RECEPCIONE + $DESPACHOE;
                    $TOTAL_MATERIALES = $RECEPCIONM + $DESPACHOM;
                    $TOTAL_ENVASES = $RECEPCIONE + $DESPACHOE;
                    $porcRecepcionMaterial = $TOTAL_ABIERTOS > 0 ? ($RECEPCIONM / $TOTAL_ABIERTOS) * 100 : 0;
                    $porcDespachoMaterial = $TOTAL_ABIERTOS > 0 ? ($DESPACHOM / $TOTAL_ABIERTOS) * 100 : 0;
                    $porcRecepcionEnvase = $TOTAL_ABIERTOS > 0 ? ($RECEPCIONE / $TOTAL_ABIERTOS) * 100 : 0;
                    $porcDespachoEnvase = $TOTAL_ABIERTOS > 0 ? ($DESPACHOE / $TOTAL_ABIERTOS) * 100 : 0;
                    ?>

                    <div class="material-dashboard">
                        <div class="row dashboard-row">
                            <div class="col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head d-flex justify-content-between align-items-center">
                                        <span>Resumen materiales</span>
                                        <small class="text-muted">Registros abiertos</small>
                                    </div>
                                    <div class="panel-card__body">
                                        <div class="summary-grid">
                                            <?php if ($PMRABIERTO == "1" && $PMATERIALES == "1" && $PMMATERIALES == "1" && $PMMRECEPION == "1") { ?>
                                                <a class="summary-tile summary-tile--green" href="listarRecepcionm.php">
                                                    <div class="summary-title">Recepción materiales</div>
                                                    <div class="summary-value"><?php echo intval($RECEPCIONM); ?></div>
                                                </a>
                                            <?php } ?>
                                            <?php if ($PMRABIERTO == "1" && $PMATERIALES == "1" && $PMMATERIALES == "1" && $PMMDEAPCHO == "1") { ?>
                                                <a class="summary-tile summary-tile--orange" href="listarDespachom.php">
                                                    <div class="summary-title">Despacho materiales</div>
                                                    <div class="summary-value"><?php echo intval($DESPACHOM); ?></div>
                                                </a>
                                            <?php } ?>
                                            <?php if ($PMRABIERTO == "1" && $PMATERIALES == "1" && $PMENVASE == "1" && $PMERECEPCION == "1") { ?>
                                                <a class="summary-tile" href="listarRecepcione.php">
                                                    <div class="summary-title">Recepción envases</div>
                                                    <div class="summary-value"><?php echo intval($RECEPCIONE); ?></div>
                                                </a>
                                            <?php } ?>
                                            <?php if ($PMRABIERTO == "1" && $PMATERIALES == "1" && $PMENVASE == "1" && $PMEDESPACHO == "1") { ?>
                                                <a class="summary-tile summary-tile--red" href="listarDespachoe.php">
                                                    <div class="summary-title">Despacho envases</div>
                                                    <div class="summary-value"><?php echo intval($DESPACHOE); ?></div>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row dashboard-row">
                            <div class="col-xl-7 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head">Distribución operativa</div>
                                    <div class="panel-card__body">
                                        <table class="table compact-table">
                                            <tbody>
                                                <tr>
                                                    <th>Recepción materiales</th>
                                                    <td class="text-right"><?php echo intval($RECEPCIONM); ?></td>
                                                    <td style="width: 130px;"><div class="status-progress"><span style="width: <?php echo $porcRecepcionMaterial; ?>%; background:#43a95c;"></span></div></td>
                                                    <td class="text-right"><?php echo number_format($porcRecepcionMaterial, 0, ",", "."); ?>%</td>
                                                </tr>
                                                <tr>
                                                    <th>Despacho materiales</th>
                                                    <td class="text-right"><?php echo intval($DESPACHOM); ?></td>
                                                    <td><div class="status-progress"><span style="width: <?php echo $porcDespachoMaterial; ?>%; background:#ef9b26;"></span></div></td>
                                                    <td class="text-right"><?php echo number_format($porcDespachoMaterial, 0, ",", "."); ?>%</td>
                                                </tr>
                                                <tr>
                                                    <th>Recepción envases</th>
                                                    <td class="text-right"><?php echo intval($RECEPCIONE); ?></td>
                                                    <td><div class="status-progress"><span style="width: <?php echo $porcRecepcionEnvase; ?>%; background:#60758f;"></span></div></td>
                                                    <td class="text-right"><?php echo number_format($porcRecepcionEnvase, 0, ",", "."); ?>%</td>
                                                </tr>
                                                <tr>
                                                    <th>Despacho envases</th>
                                                    <td class="text-right"><?php echo intval($DESPACHOE); ?></td>
                                                    <td><div class="status-progress"><span style="width: <?php echo $porcDespachoEnvase; ?>%; background:#e2525c;"></span></div></td>
                                                    <td class="text-right"><?php echo number_format($porcDespachoEnvase, 0, ",", "."); ?>%</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-5 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head">Totales</div>
                                    <div class="panel-card__body">
                                        <div class="mini-stat"><span>Total abiertos</span><strong><?php echo intval($TOTAL_ABIERTOS); ?></strong></div>
                                        <div class="mini-stat"><span>Materiales abiertos</span><strong><?php echo intval($TOTAL_MATERIALES); ?></strong></div>
                                        <div class="mini-stat"><span>Envases abiertos</span><strong><?php echo intval($TOTAL_ENVASES); ?></strong></div>
                                        <div class="mini-stat"><span>Temporada activa</span><strong><?php echo isset($TEMPORADAS) ? $TEMPORADAS : ""; ?></strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row dashboard-row">
                            <div class="col-xl-4 col-lg-6 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head">Operación diaria</div>
                                    <div class="panel-card__body">
                                        <?php if ($PMMATERIALES == "1" && $PMMRECEPION == "1") { ?><a class="quick-link" href="registroRecepcionm.php">Registrar recepción materiales</a><?php } ?>
                                        <?php if ($PMMATERIALES == "1" && $PMMDEAPCHO == "1") { ?><a class="quick-link" href="registroDespachom.php">Registrar despacho materiales</a><?php } ?>
                                        <?php if ($PMENVASE == "1" && $PMERECEPCION == "1") { ?><a class="quick-link" href="registroRecepcione.php">Registrar recepción envases</a><?php } ?>
                                        <?php if ($PMENVASE == "1" && $PMEDESPACHO == "1") { ?><a class="quick-link" href="registroDespachoe.php">Registrar despacho envases</a><?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head">Inventario</div>
                                    <div class="panel-card__body">
                                        <?php if ($PMMATERIALES == "1") { ?><a class="quick-link" href="listarInventariom.php">Existencia materiales</a><?php } ?>
                                        <?php if ($PMENVASE == "1") { ?><a class="quick-link" href="listarInventarioe.php">Existencia envases</a><?php } ?>
                                        <?php if ($PMMATERIALES == "1") { ?><a class="quick-link" href="listarInventariomHistorial.php">Historial materiales</a><?php } ?>
                                        <?php if ($PMENVASE == "1") { ?><a class="quick-link" href="listarHInventarioe.php">Kardex envases</a><?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-12">
                                <div class="panel-card">
                                    <div class="panel-card__head">Administración</div>
                                    <div class="panel-card__body">
                                        <?php if ($PMAOC == "1") { ?><a class="quick-link" href="listarOcompra.php">Órdenes de compra</a><?php } ?>
                                        <?php if ($PMAOCAR == "1") { ?><a class="quick-link" href="listarOcompraAR.php">Aprobar / rechazar órdenes</a><?php } ?>
                                        <?php if ($PMKARDEX == "1" && $PMKMATERIAL == "1") { ?><a class="quick-link" href="listarHInventariom.php">Kardex materiales</a><?php } ?>
                                        <?php if ($PMKARDEX == "1" && $PMKENVASE == "1") { ?><a class="quick-link" href="listarHInventarioe.php">Kardex envases</a><?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
        <!- LLAMADA ARCHIVO DEL DISEÑO DEL FOOTER Y MENU USUARIO -!>
            <?php include_once $rootPath . "assest/config/footer.php"; ?>
            <?php include_once $rootPath . "assest/config/menuExtraMaterial.php"; ?>
    </div>
    <?php include_once $rootPath . "assest/config/urlBase.php"; ?>
</body>
</html>
