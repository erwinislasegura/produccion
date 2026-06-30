<?php
$rootPath = __DIR__ . "/../../";
include_once $rootPath . "assest/config/validarUsuarioOpera.php";

//LLAMADA ARCHIVOS NECESARIOS PARA LAS OPERACIONES
include_once $rootPath . "assest/controlador/CONSULTA_ADO.php";
include_once $rootPath . "assest/controlador/EMPRESA_ADO.php";
include_once $rootPath . "assest/controlador/PLANTA_ADO.php";


//INICIALIZAR CONTROLADOR
$CONSULTA_ADO =  NEW CONSULTA_ADO;
$EMPRESA_ADO = new EMPRESA_ADO();
$PLANTA_ADO = new PLANTA_ADO();
//INCIALIZAR VARIBALES A OCUPAR PARA LA FUNCIONALIDAD


//INICIALIZAR ARREGLOS
$ARRAYLISTAREMPRESA="";
$ARRAYLISTARPLANTA="";

//DEFINIR ARREGLOS CON LOS DATOS OBTENIDOS DE LAS FUNCIONES DE LOS CONTROLADORES
function obtenerNeto($array)
{
    return (isset($array[0]["NETO"]) && $array[0]["NETO"] !== "") ? $array[0]["NETO"] : 0;
}

function formatearKilos($valor)
{
    return number_format((float)$valor, 0, ",", ".");
}

$ARRAYLISTAREMPRESA=$EMPRESA_ADO->listarEmpresaCBX();
if (!empty($EMPRESAS)) {
    $ARRAYLISTAREMPRESA = $EMPRESA_ADO->verEmpresa($EMPRESAS);
}
$ARRAYLISTARPLANTA=$PLANTA_ADO->listarPlantaPropiaCBX();

$ARRAYEXISTENCIAMP=$CONSULTA_ADO->existenciaDisponibleMpEst($TEMPORADAS, $ESPECIE);
$TOTALEXISTENCIAMP=obtenerNeto($ARRAYEXISTENCIAMP);

$ARRAYRECEPCIONMP=$CONSULTA_ADO->acumuladoRecepcionMpEst($TEMPORADAS, $ESPECIE);
$ARRAYRECEPCIONBULKMP=$CONSULTA_ADO->acumuladoRecepcionMpBulkEst($TEMPORADAS, $ESPECIE);
$TOTALRECECPCIOANDO=obtenerNeto($ARRAYRECEPCIONMP);
$TOTALRECECPCIOANDOBULK=obtenerNeto($ARRAYRECEPCIONBULKMP);

$ARRAYPROCESADOMP=$CONSULTA_ADO->acumuladoProcesadoMpEst($TEMPORADAS, $ESPECIE);
$TOTALPROCESADO=obtenerNeto($ARRAYPROCESADOMP);
$PORCENTAJEPROCESO = $TOTALRECECPCIOANDO > 0 ? ($TOTALPROCESADO / $TOTALRECECPCIOANDO) * 100 : 0;
$PORCENTAJEEXISTENCIA = $TOTALRECECPCIOANDO > 0 ? ($TOTALEXISTENCIAMP / $TOTALRECECPCIOANDO) * 100 : 0;


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
        <style>
            body.sistemRR { background: #f4f7fb; color: #2f3b4c; }
            .estadistica-dashboard { font-size: 13px; }
            .estadistica-dashboard .dashboard-row { margin-bottom: 10px; }
            .estadistica-dashboard .indicator-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 8px;
                margin-bottom: 10px;
            }
            .estadistica-dashboard .indicator-card {
                background: #fff;
                border: 1px solid #dce5ef;
                border-top: 3px solid #60758f;
                border-radius: 9px;
                box-shadow: 0 1px 4px rgba(25, 42, 65, 0.05);
                padding: 10px;
                min-height: 74px;
            }
            .estadistica-dashboard .indicator-card--green { border-top-color: #43a95c; }
            .estadistica-dashboard .indicator-card--orange { border-top-color: #ef9b26; }
            .estadistica-dashboard .indicator-card--red { border-top-color: #e2525c; }
            .estadistica-dashboard .indicator-title {
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: .5px;
                color: #6f8197;
                font-weight: 600;
                margin-bottom: 6px;
            }
            .estadistica-dashboard .indicator-value {
                font-size: 23px;
                line-height: 1;
                color: #24384f;
                font-weight: 600;
            }
            .estadistica-dashboard .panel-card {
                background: #fff;
                border: 1px solid #d8e1ec;
                border-radius: 8px;
                box-shadow: 0 1px 4px rgba(25, 42, 65, 0.05);
                margin-bottom: 10px;
            }
            .estadistica-dashboard .panel-card__head {
                padding: 9px 12px;
                border-bottom: 1px solid #dbe3ee;
                background: #eff3f8;
                border-radius: 8px 8px 0 0;
                font-size: 13px;
                font-weight: 600;
                color: #2d4057;
            }
            .estadistica-dashboard .status-progress {
                height: 7px;
                background: #edf2f8;
                border-radius: 999px;
                overflow: hidden;
                margin-top: 7px;
            }
            .estadistica-dashboard .status-progress span { display: block; height: 100%; border-radius: 999px; }
            .estadistica-dashboard .table-responsive { padding: 0 12px 12px; }
            .estadistica-dashboard .table th,
            .estadistica-dashboard .table td { font-size: 12px; padding: 6px 5px; vertical-align: middle; }
            @media (max-width: 1199px) { .estadistica-dashboard .indicator-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
            @media (max-width: 767px) { .estadistica-dashboard .indicator-grid { grid-template-columns: 1fr; } }
        </style>
        <!- FUNCIONES BASES -!>
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
        <!- LLAMADA AL MENU PRINCIPAL DE LA PAGINA-!>
            <?php include_once $rootPath . "assest/config/menuOpera.php"; ?>
            <div class="content-wrapper">
                <div class="container-full">
                    <section class="content estadistica-dashboard">
                        <div class="indicator-grid">
                            <div class="indicator-card indicator-card--green">
                                <div class="indicator-title">Recepción MP acumulada</div>
                                <div class="indicator-value"><?php echo formatearKilos($TOTALRECECPCIOANDO); ?> kg</div>
                            </div>
                            <div class="indicator-card indicator-card--orange">
                                <div class="indicator-title">Recepción bulk</div>
                                <div class="indicator-value"><?php echo formatearKilos($TOTALRECECPCIOANDOBULK); ?> kg</div>
                            </div>
                            <div class="indicator-card">
                                <div class="indicator-title">Procesado MP</div>
                                <div class="indicator-value"><?php echo formatearKilos($TOTALPROCESADO); ?> kg</div>
                                <div class="status-progress"><span style="width: <?php echo min($PORCENTAJEPROCESO, 100); ?>%; background:#60758f;"></span></div>
                            </div>
                            <div class="indicator-card indicator-card--red">
                                <div class="indicator-title">Existencia disponible</div>
                                <div class="indicator-value"><?php echo formatearKilos($TOTALEXISTENCIAMP); ?> kg</div>
                                <div class="status-progress"><span style="width: <?php echo min($PORCENTAJEEXISTENCIA, 100); ?>%; background:#e2525c;"></span></div>
                            </div>
                        </div>
                        <div class="row dashboard-row">
                            <?php if($PESTARVSP=="1"){ ?>
                                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 col-xs-12">
                                    <div class="panel-card">
                                        <div class="panel-card__head">Recepción VS Proceso</div>
                                        <div class="panel-card__body">
                                            <div class="table-responsive">
                                                <table class="table  table-hover" style="width: 100%;" id="resumen">
                                                    <thead>
                                                        <tr>
                                                            <th>Empresa/Planta</th>
                                                            <?php foreach ($ARRAYLISTARPLANTA as $s) : ?>
                                                                <th  class="right"> <?php echo $s["NOMBRE_PLANTA"];?> <br> Recepcion   </th>
                                                                <th  class="left"> <?php echo $s["NOMBRE_PLANTA"];?> <br> Proceso  </th>
                                                            <?php endforeach; ?>
                                                            <th class="right">Total <br> Recepción</th>
                                                            <th class="left">Total <br> Procesado</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php foreach ($ARRAYLISTAREMPRESA as $r) : ?>
                                                        <?php $ARRAYRECEPCIONMPEMPRESA=$CONSULTA_ADO->acumuladoRecepcionMpPorEmpresa($r["ID_EMPRESA"],$TEMPORADAS)?>
                                                        <?php $ARRAYPROCESADOMPEMPRESA=$CONSULTA_ADO->acumuladoProcesadoMpPorEmpresa($r["ID_EMPRESA"],$TEMPORADAS)?>
                                                            <tr >
                                                            <th> <?php echo $r["NOMBRE_EMPRESA"];?> </th>
                                                            <?php foreach ($ARRAYLISTARPLANTA as $s) : ?>
                                                                <?php $ARRAYRECEPCIONMPEMPRESAPLANTA=$CONSULTA_ADO->acumuladoRecepcionMpPorEmpresaPlanta($r["ID_EMPRESA"],$s["ID_PLANTA"],$TEMPORADAS)?>
                                                                <?php $ARRAYPROCESADOMPEMPRESAPLANTA=$CONSULTA_ADO->acumuladoProcesadoMpPorEmpresaPlanta($r["ID_EMPRESA"],$s["ID_PLANTA"],$TEMPORADAS)?>
                                                                <td class="right"><?php echo formatearKilos(obtenerNeto($ARRAYRECEPCIONMPEMPRESAPLANTA)); ?></td>
                                                                <td class="left"><?php echo formatearKilos(obtenerNeto($ARRAYPROCESADOMPEMPRESAPLANTA)); ?></td>
                                                            <?php endforeach; ?>
                                                            <td class="right"><?php echo formatearKilos(obtenerNeto($ARRAYRECEPCIONMPEMPRESA)); ?></td>
                                                            <td class="left"><?php echo formatearKilos(obtenerNeto($ARRAYPROCESADOMPEMPRESA)); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th>Sub Total</th>
                                                            <?php foreach ($ARRAYLISTARPLANTA as $s) : ?>
                                                                <?php $ARRAYRECEPCIONMPPLANTA=$CONSULTA_ADO->acumuladoRecepcionMpPorPlanta($s["ID_PLANTA"],$TEMPORADAS)?>
                                                                <?php $ARRAYPROCESADOMPPLANTA=$CONSULTA_ADO->acumuladoProcesadoMpPorPlanta($s["ID_PLANTA"],$TEMPORADAS)?>
                                                                <td class="right"><?php echo formatearKilos(obtenerNeto($ARRAYRECEPCIONMPPLANTA)); ?></td>
                                                                <td class="left"><?php echo formatearKilos(obtenerNeto($ARRAYPROCESADOMPPLANTA)); ?></td>
                                                            <?php endforeach; ?>
                                                            <td class="right"><?php echo formatearKilos($TOTALRECECPCIOANDO);?> </td>
                                                            <td class="left"><?php echo formatearKilos($TOTALPROCESADO);?> </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php  } ?>
                            <?php if($PESTASTOPMP=="1"){ ?>
                                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 col-xs-12">
                                    <div class="panel-card">
                                        <div class="panel-card__head">Existencia MP</div>
                                        <div class="panel-card__body">
                                            <div class="table-responsive">
                                                <table class="table  table-hover" style="width: 100%;"  id="stockmp">
                                                    <thead>
                                                        <tr>
                                                            <th>Empresa/Planta</th>
                                                            <?php foreach ($ARRAYLISTARPLANTA as $s) : ?>
                                                                <th> <?php echo $s["NOMBRE_PLANTA"];?> </th>
                                                            <?php endforeach; ?>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                        <tbody>
                                                            <?php foreach ($ARRAYLISTAREMPRESA as $r) : ?>
                                                                <?php $ARRAYEXISTENCIAMPEMPRESA=$CONSULTA_ADO->existenciaDisponibleMpPorEmpresa($r["ID_EMPRESA"],$TEMPORADAS);?>
                                                                        <tr >
                                                                            <th> <?php echo $r["NOMBRE_EMPRESA"];?> </th>
                                                                            <?php foreach ($ARRAYLISTARPLANTA as $s) : ?>
                                                                                <?php $ARRAYEXISTENCIAMPEMPRESAPLANTA=$CONSULTA_ADO->existenciaDisponibleMpPorEmpresaPlanta($r["ID_EMPRESA"],$s["ID_PLANTA"],$TEMPORADAS);?>
                                                                                <td><?php echo formatearKilos(obtenerNeto($ARRAYEXISTENCIAMPEMPRESAPLANTA)); ?></td>
                                                                            <?php endforeach; ?>
                                                                            <td><?php echo formatearKilos(obtenerNeto($ARRAYEXISTENCIAMPEMPRESA)); ?></td>
                                                                        </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th>Total</th>
                                                            <?php foreach ($ARRAYLISTARPLANTA as $s) : ?>
                                                                <?php $ARRAYEXISTENCIAMPEMPRESAPLANTA=$CONSULTA_ADO->existenciaDisponibleMpPorPlanta($s["ID_PLANTA"],$TEMPORADAS);?>
                                                                <td><?php echo formatearKilos(obtenerNeto($ARRAYEXISTENCIAMPEMPRESAPLANTA)); ?></td>
                                                            <?php endforeach; ?>
                                                            <td><?php echo formatearKilos($TOTALEXISTENCIAMP);?> </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php  } ?>

                        </div>
                    </section>
                </div>
            </div>
            <!- LLAMADA ARCHIVO DEL DISEÑO DEL FOOTER Y MENU USUARIO -!>
            <?php include_once $rootPath . "assest/config/footer.php"; ?>
            <?php include_once $rootPath . "assest/config/menuExtraOpera.php"; ?>
    </div>
    <!- LLAMADA URL DE ARCHIVOS DE DISEÑO Y JQUERY E OTROS -!>
        <?php include_once $rootPath . "assest/config/urlBase.php"; ?>
</body>
</html>
