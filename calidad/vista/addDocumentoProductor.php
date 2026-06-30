<?php
    $rootPath = __DIR__ . "/../../";
    include_once $rootPath . "assest/config/validarUsuarioFruta.php";

    include_once $rootPath . 'assest/controlador/productor_controller.php';

    $ID_EMPRESA = $_SESSION['ID_EMPRESA'];
    $productorController = new ProductorController();

    $productores = $productorController->index($ID_EMPRESA);
    $especies = $productorController->listaEspecie();

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <title>Calidad - Subir Documento Productor</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="">
    <meta name="author" content="">
    <!- LLAMADA DE LOS ARCHIVOS NECESARIOS PARA DISEÑO Y FUNCIONES BASE DE LA VISTA -!>
        <?php include_once $rootPath . "assest/config/urlHead.php"; ?>
        <style>
            body.sistemRR { background:#f4f7fb; color:#2f3b4c; }
            .document-panel { border:1px solid #d8e1ec; border-radius:10px; box-shadow:0 2px 8px rgba(17,32,51,.04); overflow:hidden; }
            .document-panel .box-header { background:#eff3f8 !important; border-bottom:1px solid #dbe3ee; color:#2d4057; }
            .document-panel .box-title { color:#2d4057; font-weight:600; }
            .document-panel .box-footer { background:#f7f9fc; border-top:1px solid #e1e8f2; }
            .document-panel .box-header { padding:10px 14px; }
            .document-panel .box-body { padding:14px; }
            .document-form { border-bottom:0 !important; padding:0 !important; }
            .document-form .form-control { border-radius:6px; font-size:12px; }
            .document-actions { display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; }
            .doc-btn { border-radius:999px !important; font-weight:600; letter-spacing:.1px; padding:7px 14px; box-shadow:0 1px 3px rgba(31,53,90,.08); display:inline-flex; align-items:center; justify-content:center; gap:6px; }
            .doc-btn:hover { transform:translateY(-1px); box-shadow:0 3px 8px rgba(31,53,90,.12); }
            .document-footer-actions { display:flex; gap:8px; flex-wrap:wrap; }
            @media (max-width: 767px) { .doc-btn { width:100%; margin-top:4px; } .document-footer-actions { width:100%; } }
        </style>
        <!- FUNCIONES BASES -!>


</head>

<body class="hold-transition light-skin fixed sidebar-mini theme-primary sistemRR">
    <div class="wrapper">
        <!- LLAMADA AL MENU PRINCIPAL DE LA PAGINA-!>
            <?php include_once $rootPath . "assest/config/menuCalidad.php";?>


            <div class="content-wrapper">
                <div class="container-full">
                    <!-- Content Header (Page header) -->
                    <div class="content-header">
                        <div class="d-flex align-items-center">
                            <div class="mr-auto">
                                <h3 class="page-title">Dashboard · Subir Documento Productor</h3>
                                <div class="d-inline-block align-items-center">
                                    <nav>
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"> <a href="index.php"> <i class="mdi mdi-home-outline"></i></a></li>
                                            <li class="breadcrumb-item" aria-current="page">Modulo</li>
                                            <li class="breadcrumb-item" aria-current="page">Calidad</li>
                                            <li class="breadcrumb-item" aria-current="page">Documentos por Productor</li>
                                            <li class="breadcrumb-item active" aria-current="page"> <a href="#">Subir Documento </a> </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                            <?php include_once $rootPath . "assest/config/verIndicadorEconomico.php"; ?>
                        </div>
                    </div>
                    <!-- Main content -->
                    <section class="content">
                        <div class="box document-panel">
                                <div class="box-header with-border">
                                    <div class="document-actions">
                                        <h4 class="box-title">Subir Documento</h4>
                                        <a href="listarProductorDocumento.php" class="btn btn-outline btn-secondary doc-btn"><i class="ti-back-left"></i> Volver al listado</a>
                                    </div>
                                </div>
                            <!-- /.box-header -->
                            <form data-form-layout="oneline-1" class="form form-one-line document-form" role="form-subir-documento" name="form-subir-documento" id="form-subir-documento">
                                <div class="box-body">

                                    <div class="row">
                                        <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Productor</label>
                                                <select class="form-control select2" id="productor" name="productor" style="width: 100%;">
                                                    <option value="">Seleccione una opción</option>
                                                    <?php foreach ($productores as $productor): ?>
                                                        <option value="<?= $productor->ID_PRODUCTOR ?>">(CSG: <?= $productor->CSG_PRODUCTOR ?>) <?= $productor->NOMBRE_PRODUCTOR ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Especie</label>
                                                <select class="form-control select2" id="especie" name="especie" style="width: 100%;">
                                                    <option value="">Seleccione una opción</option>
                                                    <?php foreach ($especies as $especie): ?>
                                                        <option value="<?= $especie->ID_ESPECIES ?>"><?= $especie->NOMBRE_ESPECIES ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Nombre Documento</label>
                                                <input type="text" class="form-control" placeholder="Nombre Documento" id="nombre_documento" name="nombre_documento" required/>
                                            </div>
                                        </div>

                                        <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Fecha Vigencia</label>
                                                <input type="date" class="form-control" placeholder="Fecha de Vigencia" id="fecha_vigencia" name="fecha_vigencia" required/>
                                            </div>
                                        </div>

                                        <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Adjuntar Archivo</label>
                                                <input type="file" name="documento" id="documento">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="document-footer-actions" role="group" aria-label="Acciones generales">
                                        <button type="button" class="btn btn-secondary doc-btn" data-toggle="tooltip" title="Volver" name="CANCELAR" value="CANCELAR" Onclick="javascript:history.back()">
                                            <i class="ti-back-left "></i> Volver
                                        </button>
                                        <button type="submit" class="btn btn-primary doc-btn" data-toggle="tooltip" title="Subir">
                                            <i class="ti-save-alt"></i> Guardar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.box -->
                    </section>
                    <!-- /.content -->
                </div>
            </div>


            <script type="text/javascript">


                $('body').on('submit', 'form#form-subir-documento', function(event) {
                    event.preventDefault();
                    var formData = new FormData($('form#form-subir-documento')[0]);

                    $.ajax({
                        data: formData,
                        url: "../../assest/controlador/productor_controller.php?action=uploadDocumento",
                        type: "POST",
                        cache: false,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            console.log('cargando...');
                        //$('.message').html('<img style="display:block; margin:0 auto; height: 60px;" src="./assets/img/recursos/loading.gif"/>');
                        },
                        success: function(respuesta) {

                            console.log(respuesta);

                            $.toast({
                                heading: 'Archivo cargado',
                                text: '¡Archivo cargado correctamente!',
                                position: 'bottom-left',
                                loaderBg: '#ff6849',
                                icon: 'success',
                                hideAfter: 3500,
                                stack: 6
                            });

                            $("form#form-subir-documento")[0].reset();

                            /*if (respuesta == 1) {
                            $('.message').html('<div class="alert alert-success alert-dismissible"><i class="fa fa-check"></i> Información ingresada correctamente.</div>');
                            $("form#form")[0].reset();

                            } else {
                                $('.message').html('<div class="alert alert-danger alert-dismissible"><i class="fa fa-times"></i> Ops! Error al ingresar la información.</div>');
                            }*/
                        }
                    });
                    return false;
                });



        </script>

            <!- LLAMADA ARCHIVO DEL DISEÑO DEL FOOTER Y MENU USUARIO -!>
                <?php include_once $rootPath . "assest/config/footer.php"; ?>
                <?php include_once $rootPath . "assest/config/menuExtraFruta.php"; ?>
    </div>
    <!- LLAMADA URL DE ARCHIVOS DE DISEÑO Y JQUERY E OTROS -!>
        <?php include_once $rootPath . "assest/config/urlBase.php"; ?>
</body>

</html>
