<?php
include_once "../../assest/config/validarUsuarioFruta.php";
include_once '../../assest/controlador/InspectionMPController.php';
include_once '../../assest/controlador/USUARIO_ADO.php';

$INSPECTION_MP_CONTROLLER = new InspectionMPController();
$USUARIO_ADO = new USUARIO_ADO();

$ARRAYINSPECTIONMP = [];
$MENSAJE = "";
$MENSAJEENVIO = "";

$CORREOUSUARIO = "";
$NOMBRECOMPLETOUSUARIO = $_SESSION['NOMBRE_USUARIO'] ?? '';
$ARRAYUSUARIO = $USUARIO_ADO->verUsuario($_SESSION["ID_USUARIO"]);
if ($ARRAYUSUARIO) {
    $CORREOUSUARIO = trim($ARRAYUSUARIO[0]['EMAIL_USUARIO']);
    $NOMBRECOMPLETOUSUARIO = trim(
        ($ARRAYUSUARIO[0]['PNOMBRE_USUARIO'] ?? '') . ' ' .
        ($ARRAYUSUARIO[0]['SNOMBRE_USUARIO'] ?? '') . ' ' .
        ($ARRAYUSUARIO[0]['PAPELLIDO_USUARIO'] ?? '') . ' ' .
        ($ARRAYUSUARIO[0]['SAPELLIDO_USUARIO'] ?? '')
    );
    $NOMBRECOMPLETOUSUARIO = trim($NOMBRECOMPLETOUSUARIO) ?: ($_SESSION['NOMBRE_USUARIO'] ?? '');
}

function generarCodigoAutorizacionInspection()
{
    return function_exists('random_int') ? random_int(100000, 999999) : mt_rand(100000, 999999);
}

function obtenerDestinatariosAutorizacionInspection($correoSolicitante)
{
    $correosBase = ['maperez@fvolcan.cl', 'eisla@fvolcan.cl'];
    $correoSolicitante = trim((string)$correoSolicitante);
    if ($correoSolicitante !== '') {
        $correosBase = array_filter($correosBase, fn($correo) => strcasecmp($correo, $correoSolicitante) !== 0);
    }
    return array_values(array_filter(array_unique($correosBase)));
}

function enviarCorreoSMTPInspection($destinatarios, $asunto, $mensaje, $remitente, $usuario, $contrasena, $host, $puerto, $timeout = 30)
{
    $destinatarios = (array)$destinatarios;
    $contextoSSL = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'crypto_method' => STREAM_CRYPTO_METHOD_TLS_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
        ]
    ]);

    $conexion = @stream_socket_client("ssl://{$host}:{$puerto}", $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $contextoSSL);
    if (!$conexion) {
        return [false, "No se pudo conectar al servidor SMTP ({$errstr})"];
    }
    if (function_exists('stream_set_timeout')) {
        stream_set_timeout($conexion, $timeout);
    }

    $leerRespuesta = function () use ($conexion) {
        $respuesta = '';
        while ($linea = fgets($conexion, 515)) {
            $respuesta .= $linea;
            if (isset($linea[3]) && $linea[3] === ' ') {
                break;
            }
        }
        return $respuesta;
    };

    $comando = function ($instruccion, $codigoEsperado) use ($conexion, $leerRespuesta) {
        fwrite($conexion, $instruccion . "\r\n");
        $respuesta = $leerRespuesta();
        if (substr($respuesta, 0, 3) !== $codigoEsperado) {
            throw new Exception("Error SMTP en '{$instruccion}': {$respuesta}");
        }
        return $respuesta;
    };

    $respuestaInicial = $leerRespuesta();
    if (substr($respuestaInicial, 0, 3) !== '220') {
        fclose($conexion);
        return [false, "El servidor SMTP no respondió correctamente: {$respuestaInicial}"];
    }

    $hostEhlo = $host ?: 'localhost';
    try {
        $comando('EHLO ' . $hostEhlo, '250');
    } catch (Exception $e) {
        $comando('HELO ' . $hostEhlo, '250');
    }

    try {
        $comando('AUTH LOGIN', '334');
        $comando(base64_encode($usuario), '334');
        $comando(base64_encode($contrasena), '235');
    } catch (Exception $e) {
        fclose($conexion);
        return [false, "Error de autenticación SMTP: " . $e->getMessage()];
    }

    try {
        $comando("MAIL FROM:<{$remitente}>", '250');
        foreach ($destinatarios as $correo) {
            $comando("RCPT TO:<{$correo}>", '250');
        }
        $comando('DATA', '354');

        $cabeceras = "Date: " . date('r') . "\r\n" .
            "Message-ID: <" . uniqid() . "@" . ($hostEhlo ?: 'localhost') . ">\r\n" .
            "From: {$remitente}\r\n" .
            "Return-Path: {$remitente}\r\n" .
            "Reply-To: {$remitente}\r\n" .
            "To: " . implode(', ', $destinatarios) . "\r\n" .
            "Subject: {$asunto}\r\n" .
            "MIME-Version: 1.0\r\n" .
            "X-Mailer: PHP/" . phpversion() . "\r\n" .
            "Content-Type: text/plain; charset=UTF-8\r\n\r\n";

        $mensajeNormalizado = str_replace(["\r\n", "\n"], "\r\n", $mensaje);
        fwrite($conexion, $cabeceras . $mensajeNormalizado . "\r\n.\r\n");
        $respuestaData = $leerRespuesta();
        if (substr($respuestaData, 0, 3) !== '250') {
            throw new Exception("Error SMTP tras DATA: {$respuestaData}");
        }
        $comando('QUIT', '221');
    } catch (Exception $e) {
        fclose($conexion);
        return [false, "Error al enviar correo: " . $e->getMessage()];
    }

    fclose($conexion);
    return [true, null];
}

if ($_POST) {
    $IDINSPECCION = isset($_REQUEST['ID']) ? (int)$_REQUEST['ID'] : 0;
    $CODIGOCIERRE = trim((string)($_REQUEST['CODIGO_CERRAR'] ?? ''));
    $CODIGOAPERTURA = trim((string)($_REQUEST['CODIGO_ABRIR'] ?? ''));

    $datosInspeccion = $IDINSPECCION > 0 ? $INSPECTION_MP_CONTROLLER->getInspectionSummaryById($IDINSPECCION) : null;

    if (!$datosInspeccion && $IDINSPECCION > 0) {
        $MENSAJE = 'No se encontró la inspección seleccionada.';
    } else {
        $destinatarios = obtenerDestinatariosAutorizacionInspection($CORREOUSUARIO);
        $remitente = 'informes@volcanfoods.cl';
        $usuarioSMTP = 'informes@volcanfoods.cl';
        $contrasenaSMTP = '1z=EWfu0026k';
        $hostSMTP = 'mail.volcanfoods.cl';
        $puertoSMTP = 465;

        if (isset($_REQUEST['SOLICITAR_CERRAR'])) {
            $codigo = generarCodigoAutorizacionInspection();
            $_SESSION['INSPECTION_CERRAR_CODIGO'] = $codigo;
            $_SESSION['INSPECTION_CERRAR_ID'] = $IDINSPECCION;
            $_SESSION['INSPECTION_CERRAR_TIEMPO'] = time();

            $asunto = 'Autorización cierre inspección MP #' . $datosInspeccion['inspection_number'];
            $mensajeCorreo = "Se solicitó cerrar/eliminar una inspección MP.\r\n\r\n" .
                "N° inspección: " . $datosInspeccion['inspection_number'] . "\r\n" .
                "N° recepción: " . $datosInspeccion['reception_number'] . "\r\n" .
                "Fecha inspección: " . $datosInspeccion['inspection_date'] . "\r\n" .
                "Productor: " . $datosInspeccion['producer_name'] . "\r\n" .
                "Inspector: " . $datosInspeccion['inspector_name'] . "\r\n" .
                "Solicitado por: " . $NOMBRECOMPLETOUSUARIO . "\r\n" .
                "Código de autorización: " . $codigo . "\r\n\r\n" .
                "El código tiene validez de 15 minutos.";

            [$envioOk, $errorEnvio] = enviarCorreoSMTPInspection($destinatarios, $asunto, $mensajeCorreo, $remitente, $usuarioSMTP, $contrasenaSMTP, $hostSMTP, $puertoSMTP);
            if ($envioOk) {
                $MENSAJEENVIO = 'Código de autorización enviado correctamente a Maria de los Ángeles y Erwin Isla.';
            } else {
                $MENSAJE = $errorEnvio ?: 'No fue posible enviar el correo de autorización.';
            }
        }

        if (isset($_REQUEST['CONFIRMAR_CERRAR'])) {
            $codigoSesion = $_SESSION['INSPECTION_CERRAR_CODIGO'] ?? null;
            $idSesion = $_SESSION['INSPECTION_CERRAR_ID'] ?? null;
            $tiempoSesion = $_SESSION['INSPECTION_CERRAR_TIEMPO'] ?? 0;

            if (!$codigoSesion || !$idSesion || (int)$idSesion !== $IDINSPECCION) {
                $MENSAJE = 'Debe solicitar un código de autorización antes de cerrar/eliminar esta inspección.';
            } elseif ((time() - $tiempoSesion) > 900) {
                $MENSAJE = 'El código de autorización ha expirado.';
            } elseif ($CODIGOCIERRE === '' || $CODIGOCIERRE !== (string)$codigoSesion) {
                $MENSAJE = 'El código ingresado no es válido.';
            } else {
                $INSPECTION_MP_CONTROLLER->deleteInspection($IDINSPECCION, $IDUSUARIOS);
                unset($_SESSION['INSPECTION_CERRAR_CODIGO'], $_SESSION['INSPECTION_CERRAR_ID'], $_SESSION['INSPECTION_CERRAR_TIEMPO']);
                $MENSAJEENVIO = 'Inspección cerrada/eliminada correctamente.';
            }
        }

        if (isset($_REQUEST['SOLICITAR_ABRIR'])) {
            $codigo = generarCodigoAutorizacionInspection();
            $_SESSION['INSPECTION_ABRIR_CODIGO'] = $codigo;
            $_SESSION['INSPECTION_ABRIR_ID'] = $IDINSPECCION;
            $_SESSION['INSPECTION_ABRIR_TIEMPO'] = time();

            $asunto = 'Autorización apertura inspección MP #' . $datosInspeccion['inspection_number'];
            $mensajeCorreo = "Se solicitó abrir una inspección MP.\r\n\r\n" .
                "N° inspección: " . $datosInspeccion['inspection_number'] . "\r\n" .
                "N° recepción: " . $datosInspeccion['reception_number'] . "\r\n" .
                "Fecha inspección: " . $datosInspeccion['inspection_date'] . "\r\n" .
                "Productor: " . $datosInspeccion['producer_name'] . "\r\n" .
                "Inspector: " . $datosInspeccion['inspector_name'] . "\r\n" .
                "Solicitado por: " . $NOMBRECOMPLETOUSUARIO . "\r\n" .
                "Código de autorización: " . $codigo . "\r\n\r\n" .
                "El código tiene validez de 15 minutos.";

            [$envioOk, $errorEnvio] = enviarCorreoSMTPInspection($destinatarios, $asunto, $mensajeCorreo, $remitente, $usuarioSMTP, $contrasenaSMTP, $hostSMTP, $puertoSMTP);
            if ($envioOk) {
                $MENSAJEENVIO = 'Código de autorización enviado correctamente a Maria de los Ángeles y Erwin Isla.';
            } else {
                $MENSAJE = $errorEnvio ?: 'No fue posible enviar el correo de autorización.';
            }
        }

        if (isset($_REQUEST['CONFIRMAR_ABRIR'])) {
            $codigoSesion = $_SESSION['INSPECTION_ABRIR_CODIGO'] ?? null;
            $idSesion = $_SESSION['INSPECTION_ABRIR_ID'] ?? null;
            $tiempoSesion = $_SESSION['INSPECTION_ABRIR_TIEMPO'] ?? 0;

            if (!$codigoSesion || !$idSesion || (int)$idSesion !== $IDINSPECCION) {
                $MENSAJE = 'No hay una solicitud de apertura vigente para esta inspección.';
            } elseif ((time() - $tiempoSesion) > 900) {
                $MENSAJE = 'El código de autorización ha expirado.';
            } elseif ($CODIGOAPERTURA === '' || $CODIGOAPERTURA !== (string)$codigoSesion) {
                $MENSAJE = 'El código ingresado no es válido.';
            } else {
                $INSPECTION_MP_CONTROLLER->updateInspectionState($IDINSPECCION, 1, $IDUSUARIOS);
                unset($_SESSION['INSPECTION_ABRIR_CODIGO'], $_SESSION['INSPECTION_ABRIR_ID'], $_SESSION['INSPECTION_ABRIR_TIEMPO']);
                $MENSAJEENVIO = 'Inspección abierta correctamente.';
            }
        }
    }
}

if ($EMPRESAS && $PLANTAS && $TEMPORADAS) {
    $ARRAYINSPECTIONMP = $INSPECTION_MP_CONTROLLER->getInspectionGrouped($EMPRESAS, $PLANTAS, $TEMPORADAS);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Agrupado MP</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php include_once "../../assest/config/urlHead.php"; ?>
    <script type="text/javascript">
        function abrirPestana(url) {
            var win = window.open(url, '_blank');
            if (win) {
                win.focus();
            }
        }
    </script>
    <style>
        .btn-autorizacion {
            white-space: normal;
            line-height: 1.15;
        }
    </style>
</head>
<body class="hold-transition light-skin fixed sidebar-mini theme-primary sistemRR">
    <div class="wrapper">
        <?php include_once "../../assest/config/menuCalidad.php"; ?>
        <div class="content-wrapper">
            <div class="container-full">
                <div class="content-header">
                    <div class="d-flex align-items-center">
                        <div class="mr-auto">
                            <h3 class="page-title">Calidad de Fruta</h3>
                            <div class="d-inline-block align-items-center">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                                        <li class="breadcrumb-item" aria-current="page">Inspecciones</li>
                                        <li class="breadcrumb-item active" aria-current="page"><a href="#">Agrupado MP</a></li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <?php include_once "../../assest/config/verIndicadorEconomico.php"; ?>
                    </div>
                </div>

                <section class="content">
                    <?php if (!empty($MENSAJE)) : ?>
                        <div class="alert alert-danger" role="alert"><?php echo $MENSAJE; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($MENSAJEENVIO)) : ?>
                        <div class="alert alert-success" role="alert"><?php echo $MENSAJEENVIO; ?></div>
                    <?php endif; ?>

                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Agrupado MP</h4>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="agrupadoinspectionmp" class="table table-hover" style="width:100%;">
                                    <thead>
                                        <tr class="text-center">
                                            <th>ID</th>
                                            <th>Estado</th>
                                            <th>Administración</th>
                                            <th>Autorizaciones</th>
                                            <th>N° Inspección</th>
                                            <th>Fecha Inspección</th>
                                            <th>N° Recepción</th>
                                            <th>N° Guía</th>
                                            <th>Productor</th>
                                            <th>Inspector</th>
                                            <th>Producto</th>
                                            <th>Total Pallets</th>
                                            <th>Filas Detalle</th>
                                            <th>Score</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($ARRAYINSPECTIONMP as $r) : ?>
                                            <?php $estaAbierta = ((int)$r['estado'] === 1); ?>
                                            <tr class="text-center">
                                                <td><?php echo $r['id']; ?></td>
                                                <td>
                                                    <?php if ($estaAbierta) : ?>
                                                        <button type="button" class="btn btn-success btn-sm btn-block">Abierta</button>
                                                    <?php else : ?>
                                                        <button type="button" class="btn btn-danger btn-sm btn-block">Cerrada</button>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="list-icons d-inline-flex">
                                                        <div class="list-icons-item dropdown">
                                                            <button class="btn btn-secondary btn-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="glyphicon glyphicon-cog"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <?php if ($estaAbierta) : ?>
                                                                    <a class="dropdown-item" href="../../calidad/inspection-mp/index.php?inspection_id=<?php echo (int)$r['id']; ?>">
                                                                        <i class="ti-pencil-alt"></i> Editar
                                                                    </a>
                                                                <?php else : ?>
                                                                    <span class="dropdown-item text-muted">
                                                                        <i class="ti-lock"></i> Editar bloqueado
                                                                    </span>
                                                                <?php endif; ?>
                                                                <button type="button" class="dropdown-item" onclick="abrirPestana('../../calidad/inspection-mp/export.php?id=<?php echo (int)$r['id']; ?>')">
                                                                    <i class="fa fa-file-pdf-o"></i> Informe
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group-vertical w-100">
                                                        <?php if ($estaAbierta) : ?>
                                                            <button type="button" class="btn btn-outline-danger btn-sm btn-autorizacion" data-toggle="modal" data-target="#modalCerrarInspeccion" data-id="<?php echo (int)$r['id']; ?>" data-numero="<?php echo htmlspecialchars($r['inspection_number'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                <i class="fa fa-trash"></i> Eliminar
                                                            </button>
                                                        <?php else : ?>
                                                            <button type="button" class="btn btn-outline-success btn-sm btn-autorizacion" data-toggle="modal" data-target="#modalAbrirInspeccion" data-id="<?php echo (int)$r['id']; ?>" data-numero="<?php echo htmlspecialchars($r['inspection_number'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                <i class="fa fa-unlock"></i> Abrir
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td><?php echo $r['inspection_number']; ?></td>
                                                <td><?php echo $r['inspection_date']; ?></td>
                                                <td><?php echo $r['reception_number']; ?></td>
                                                <td><?php echo $r['guide_number']; ?></td>
                                                <td><?php echo $r['producer_name']; ?></td>
                                                <td><?php echo $r['inspector_name']; ?></td>
                                                <td><?php echo $r['product_name']; ?></td>
                                                <td><?php echo $r['total_pallets']; ?></td>
                                                <td><?php echo $r['detail_rows']; ?></td>
                                                <td><?php echo number_format((float)$r['score'], 2, ',', '.'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <?php include_once "../../assest/config/footer.php"; ?>
        <?php include_once "../../assest/config/menuExtraFruta.php"; ?>
    </div>

    <?php include_once "../../assest/config/urlBase.php"; ?>

    <div class="modal fade" id="modalCerrarInspeccion" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Autorización para cerrar/eliminar inspección</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form class="form-one-line" data-form-layout="oneline-1" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.65rem;padding:0.65rem 0;border-bottom:1px solid #3a3a3a;" method="post">
                    <div class="modal-body">
                        <p class="mb-3">El código se envía a Maria de los Ángeles y Erwin Isla.</p>
                        <p class="font-weight-bold">Inspección N° <span class="numero-inspeccion-cerrar"></span></p>
                        <input type="hidden" name="ID" value="">
                        <div class="form-group">
                            <label for="codigoCerrar">Código de autorización</label>
                            <input type="text" class="form-control" id="codigoCerrar" name="CODIGO_CERRAR" placeholder="Ingresa el código recibido">
                            <small class="form-text text-muted">El código tiene validez de 15 minutos.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-danger" name="SOLICITAR_CERRAR">Solicitar código</button>
                        <button type="submit" class="btn btn-danger" name="CONFIRMAR_CERRAR">Cerrar/Eliminar inspección</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAbrirInspeccion" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Autorización para abrir inspección</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form class="form-one-line" data-form-layout="oneline-2" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.65rem;padding:0.65rem 0;border-bottom:1px solid #3a3a3a;" method="post">
                    <div class="modal-body">
                        <p class="mb-3">El código se envía a Maria de los Ángeles y Erwin Isla.</p>
                        <p class="font-weight-bold">Inspección N° <span class="numero-inspeccion-abrir"></span></p>
                        <input type="hidden" name="ID" value="">
                        <div class="form-group">
                            <label for="codigoAbrir">Código de autorización</label>
                            <input type="text" class="form-control" id="codigoAbrir" name="CODIGO_ABRIR" placeholder="Ingresa el código recibido">
                            <small class="form-text text-muted">El código tiene validez de 15 minutos.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-success" name="SOLICITAR_ABRIR">Solicitar código</button>
                        <button type="submit" class="btn btn-success" name="CONFIRMAR_ABRIR">Abrir inspección</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            if ($.fn.DataTable.isDataTable('#agrupadoinspectionmp')) {
                $('#agrupadoinspectionmp').DataTable().destroy();
            }

            $('#agrupadoinspectionmp').DataTable({
                order: [[0, 'desc']],
                paging: false,
                scrollX: true,
                lengthChange: false,
                dom: 'Bfrtip',
                buttons: [{ extend: 'excelHtml5', text: 'Excel', exportOptions: { columns: ':visible' } }],
                language: { search: "Buscar:" }
            });

            $('#modalCerrarInspeccion').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var numero = button.data('numero');
                var modal = $(this);
                modal.find('input[name="ID"]').val(id);
                modal.find('.numero-inspeccion-cerrar').text(numero || '');
                modal.find('input[name="CODIGO_CERRAR"]').val('');
            });

            $('#modalAbrirInspeccion').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var numero = button.data('numero');
                var modal = $(this);
                modal.find('input[name="ID"]').val(id);
                modal.find('.numero-inspeccion-abrir').text(numero || '');
                modal.find('input[name="CODIGO_ABRIR"]').val('');
            });
        });
    </script>
</body>
</html>
