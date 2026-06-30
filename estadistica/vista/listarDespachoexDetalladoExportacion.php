<?php

include_once "../../assest/config/validarUsuarioOpera.php";


//LLAMADA ARCHIVOS NECESARIOS PARA LAS OPERACIONES
include_once __DIR__ . '/helpers/despachoexDetalladoExportacionData.php';


if (isset($_GET['precarga'])) {
    cargarDespachoexDetalladoExportacionData($TEMPORADAS, $EMPRESAS);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'ok']);
    exit;
}

//INCIALIZAR VARIBALES A OCUPAR PARA LA FUNCIONALIDAD
$TOTALBRUTO = 0;
$TOTALNETO = 0;
$TOTALENVASE = 0;
$FECHADESDE = "";
$FECHAHASTA = "";

$PRODUCTOR = "";
$NUMEROGUIA = "";

//INICIALIZAR ARREGLOS
$ARRAYDESPACHOPT = [];
$ARRAYDESPACHOPTTOTALES = [];
$ARRAYVEREMPRESA = [];
$ARRAYVERPRODUCTOR = [];
$ARRAYVERTRANSPORTE = [];
$ARRAYVERCONDUCTOR = [];
$ARRAYMGUIAPT = [];
$ARRAYRECEPCIONMPORIGEN1 = [];
$ARRAYRECEPCIONMPORIGEN2 = [];

$DATA_EXPORTACION = cargarDespachoexDetalladoExportacionData($TEMPORADAS, $EMPRESAS);
$ARRAYDETALLADOEXPORTACION = $DATA_EXPORTACION['rows'];

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Detallado de exportacion</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- LLAMADA DE LOS ARCHIVOS NECESARIOS PARA DISEÑO Y FUNCIONES BASE DE LA VISTA -->
    <?php include_once "../../assest/config/urlHead.php"; ?>
    <!-- FUNCIONES BASES -->
    <script type="text/javascript">
        //REDIRECCIONAR A LA PAGINA SELECIONADA
        function irPagina(url) {
            location.href = "" + url;
        }

        function refrescar() {
            document.getElementById("form_reg_dato").submit();
        }

        function abrirPestana(url) {
            var win = window.open(url, '_blank');
            win.focus();
        }
        //FUNCION PARA ABRIR VENTANA QUE SE ENCUENTRA LA OPERACIONES DE DETALLE DE RECEPCION
        function abrirVentana(url) {
            var opciones =
                "'directories=no, location=no, menubar=no, scrollbars=yes, statusbar=no, tittlebar=no, width=1000, height=800'";
            window.open(url, 'window', opciones);
        }
    </script>
</head>

<body class="hold-transition light-skin fixed sidebar-mini theme-primary sistemRR">
    <div class="wrapper">
        <?php include_once "../../assest/config/menuOpera.php"; ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="container-full">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="d-flex align-items-center">
                        <div class="mr-auto">
                            <h3 class="page-title">Detallado </h3>
                            <div class="d-inline-block align-items-center">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                                        <li class="breadcrumb-item" aria-current="page">Módulo</li>
                                        <li class="breadcrumb-item" aria-current="page">Detallado</li>
                                        <li class="breadcrumb-item active" aria-current="page">
                                            <a href="#"> Detallado de exportacion </a>
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <?php include_once "../../assest/config/verIndicadorEconomico.php"; ?>
                    </div>
                </div>
                <!-- Main content -->
                <section class="content">
                    <div class="box">

                        <div class="box-body">
                            <div class="row">
                                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 col-xs-12">
                                    <div class="table-responsive">
                                        <table id="detalladodex" class="table-hover" style="width: 100%;">
                                            <thead>
                                                <tr class="text-center">
                                                    <th>Número Referencia </th>
                                                    <th>Cliente</th>
                                                    <th>Mercado </th>
                                                    <th>Contenedor </th>
                                                    <th>Tipo Despacho </th>
                                                    <th>Número Despacho </th>
                                                    <th>Fecha Despacho </th>
                                                    <th>Número Guía Despacho </th>
                                                    <th>Destino </th>
                                                    <th>Fecha Corte Documental </th>
                                                    <th>Fecha ETD </th>
                                                    <th>Fecha Real ETD</th>
                                                    <th>Fecha ETA</th>
                                                    <th>Fecha Real ETA</th>
                                                    <th>Recibidor Final</th>
                                                    <th>Tipo Embarque</th>
                                                    <th>Nave</th>
                                                    <th>Número Viaje/Vuelo</th>
                                                    <th>Puerto/Aeropuerto/Lugar Destino</th>
                                                    <th>N° Folio Original</th>
                                                    <th>N° Folio </th>
                                                    <th>Fecha Embalado </th>
                                                    <th>Condición </th>
                                                    <th>Código Estandar</th>
                                                    <th>Envase/Estandar</th>
                                                    <th>CSG</th>
                                                    <th>Productor</th>
                                                    <th>Especies</th>
                                                    <th>Variedad</th>
                                                    <th>Cantidad Envase</th>
                                                    <th>Kilos Neto</th>
                                                    <th>% Deshidratacion</th>
                                                    <th>Kilos Deshidratacion</th>
                                                    <th>Kilos Bruto</th>
                                                    <th>Número Repaletizaje </th>
                                                    <th>Fecha Repaletizaje </th>
                                                    <th>Número Proceso </th>
                                                    <th>Fecha Proceso </th>
                                                    <th>Tipo Proceso </th>
                                                    <th>Número Reembalaje </th>
                                                    <th>Fecha Reembalaje </th>
                                                    <th>Tipo Reembalaje </th>
                                                    <th>Tipo Manejo</th>
                                                    <th>Tipo Calibre </th>
                                                    <th>Tipo Embalaje </th>
                                                    <th>Stock</th>
                                                    <th>Embolsado</th>
                                                    <th>Gasificacion</th>
                                                    <th>Prefrío</th>
                                                    <th>Transporte </th>
                                                    <th>Nombre Conductor </th>
                                                    <th>Patente Camión </th>
                                                    <th>Patente Carro </th>
                                                    <th>Semana Despacho </th>
                                                    <th>Semana Guía </th>
                                                    <th>Empresa</th>
                                                    <th>Planta</th>
                                                    <th>Temporada</th>
                                                    <th>Bl/AWB</th>
                                                    <th>Número Recepción </th>
                                                    <th>Fecha Recepción </th>
                                                    <th>Tipo Recepción </th>
                                                    <th>Número Guía Recepción </th>
                                                    <th>Fecha Guía Recepción</th>
                                                    <th>Número Recepción MP</th>
                                                    <th>Fecha Recepción MP</th>
                                                    <th>Tipo Recepción MP</th>
                                                    <th>Número Guía Recepción MP</th>
                                                    <th>Fecha Guía Recepción MP </th>
                                                    <th>Planta Recepción MP</th>
                                                    <th>Termógrafo Despacho</th>
                                                    <th>Termógrafo Pallet</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($ARRAYDETALLADOEXPORTACION as $row) : ?>
                                                    <tr class="text-center">
                                                        <td><?php echo $row['Número Referencia']; ?></td>
                                                        <td><?php echo $row['Cliente']; ?></td>
                                                        <td><?php echo $row['Mercado']; ?></td>
                                                        <td><?php echo $row['Contenedor']; ?></td>
                                                        <td><?php echo $row['Tipo Despacho']; ?></td>
                                                        <td><?php echo $row['Número Despacho']; ?></td>
                                                        <td><?php echo $row['Fecha Despacho']; ?></td>
                                                        <td><?php echo $row['Número Guía Despacho']; ?></td>
                                                        <td><?php echo $row['Destino']; ?></td>
                                                        <td><?php echo $row['Fecha Corte Documental']; ?></td>
                                                        <td><?php echo $row['Fecha ETD']; ?></td>
                                                        <td><?php echo $row['Fecha Real ETD']; ?></td>
                                                        <td><?php echo $row['Fecha ETA']; ?></td>
                                                        <td><?php echo $row['Fecha Real ETA']; ?></td>
                                                        <td><?php echo $row['Recibidor Final']; ?></td>
                                                        <td><?php echo $row['Tipo Embarque']; ?></td>
                                                        <td><?php echo $row['Nave']; ?></td>
                                                        <td><?php echo $row['Número Viaje/Vuelo']; ?></td>
                                                        <td><?php echo $row['Puerto/Aeropuerto/Lugar Destino']; ?></td>
                                                        <td><?php echo $row['N° Folio Original']; ?></td>
                                                        <td><?php echo $row['N° Folio']; ?></td>
                                                        <td><?php echo $row['Fecha Embalado']; ?></td>
                                                        <td><?php echo $row['Condición']; ?></td>
                                                        <td><?php echo $row['Código Estandar']; ?></td>
                                                        <td><?php echo $row['Envase/Estandar']; ?></td>
                                                        <td><?php echo $row['CSG']; ?></td>
                                                        <td><?php echo $row['Productor']; ?></td>
                                                        <td><?php echo $row['Especies']; ?></td>
                                                        <td><?php echo $row['Variedad']; ?></td>
                                                        <td><?php echo $row['Cantidad Envase']; ?></td>
                                                        <td><?php echo $row['Kilos Neto']; ?></td>
                                                        <td><?php echo $row['% Deshidratación']; ?></td>
                                                        <td><?php echo $row['Kilos Deshidratación']; ?></td>
                                                        <td><?php echo $row['Kilos Bruto']; ?></td>
                                                        <td><?php echo $row['Número Repaletizaje']; ?></td>
                                                        <td><?php echo $row['Fecha Repaletizaje']; ?></td>
                                                        <td><?php echo $row['Número Proceso']; ?></td>
                                                        <td><?php echo $row['Fecha Proceso']; ?></td>
                                                        <td><?php echo $row['Tipo Proceso']; ?></td>
                                                        <td><?php echo $row['Número Reembalaje']; ?></td>
                                                        <td><?php echo $row['Fecha Reembalaje']; ?></td>
                                                        <td><?php echo $row['Tipo Reembalaje']; ?></td>
                                                        <td><?php echo $row['Tipo Manejo']; ?></td>
                                                        <td><?php echo $row['Tipo Calibre']; ?></td>
                                                        <td><?php echo $row['Tipo Embalaje']; ?></td>
                                                        <td><?php echo $row['Stock']; ?></td>
                                                        <td><?php echo $row['Embolsado']; ?></td>
                                                        <td><?php echo $row['Gasificación']; ?></td>
                                                        <td><?php echo $row['Prefrío']; ?></td>
                                                        <td><?php echo $row['Transporte']; ?></td>
                                                        <td><?php echo $row['Nombre Conductor']; ?></td>
                                                        <td><?php echo $row['Patente Camión']; ?></td>
                                                        <td><?php echo $row['Patente Carro']; ?></td>
                                                        <td><?php echo $row['Semana']; ?></td>
                                                        <td><?php echo $row['Semana Guía']; ?></td>
                                                        <td><?php echo $row['Empresa']; ?></td>
                                                        <td><?php echo $row['Planta']; ?></td>
                                                        <td><?php echo $row['Temporada']; ?></td>
                                                        <td><?php echo $row['Bl/AWB']; ?></td>
                                                        <td><?php echo $row['Número Recepción']; ?></td>
                                                        <td><?php echo $row['Fecha Recepción']; ?></td>
                                                        <td><?php echo $row['Tipo Recepción']; ?></td>
                                                        <td><?php echo $row['Número Guía Recepción']; ?></td>
                                                        <td><?php echo $row['Fecha Guía Recepción']; ?></td>
                                                        <td><?php echo $row['Número Recepción MP']; ?></td>
                                                        <td><?php echo $row['Fecha Recepción MP']; ?></td>
                                                        <td><?php echo $row['Tipo Recepción MP']; ?></td>
                                                        <td><?php echo $row['Número Guía Recepción MP']; ?></td>
                                                        <td><?php echo $row['Fecha Guía Recepción MP']; ?></td>
                                                        <td><?php echo $row['Planta Recepción MP']; ?></td>
                                                        <td><?php echo $row['Termógrafo Despacho']; ?></td>
                                                        <td><?php echo $row['Termógrafo Pallet']; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <div class="btn-toolbar mb-3" role="toolbar" aria-label="Datos generales">
                                    <div class="form-row align-items-center" role="group" aria-label="Datos">
                                        <div class="col-auto">
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">Total Envase</div>
                                                    <button class="btn btn-default" id="TOTALENVASEV" name="TOTALENVASEV">
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">Total Neto</div>
                                                    <button class="btn btn-default" id="TOTALNETOV" name="TOTALNETOV">
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">Total Bruto</div>
                                                    <button class="btn btn-default" id="TOTALBRUTOV" name="TOTALBRUTOV">
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                        </div>
                        <!-- /.box -->
                    </div>
                </section>
                <!-- /.content -->
            </div>
        </div>
        <?php include_once "../../assest/config/footer.php"; ?>
        <?php include_once "../../assest/config/menuExtraOpera.php"; ?>
    </div>
    <?php include_once "../../assest/config/urlBase.php"; ?>
</body>
</html>
