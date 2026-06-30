<?php
include_once "../../assest/config/validarUsuarioExpo.php";
include_once '../../assest/controlador/OPROCESO_ADO.php';
include_once '../../assest/controlador/MERCADO_ADO.php';
include_once '../../assest/controlador/PRODUCTOR_ADO.php';
include_once '../../assest/controlador/VESPECIES_ADO.php';
include_once '../../assest/controlador/EEXPORTACION_ADO.php';
include_once '../../assest/controlador/RMERCADO_ADO.php';
include_once '../../assest/controlador/REEXPORTACIONMERCADO_ADO.php';
include_once '../../assest/modelo/OPROCESO.php';

$OPROCESO_ADO = new OPROCESO_ADO();
$MERCADO_ADO = new MERCADO_ADO();
$PRODUCTOR_ADO = new PRODUCTOR_ADO();
$VESPECIES_ADO = new VESPECIES_ADO();
$EEXPORTACION_ADO = new EEXPORTACION_ADO();
$RMERCADO_ADO = new RMERCADO_ADO();
$REEXPORTACIONMERCADO_ADO = new REEXPORTACIONMERCADO_ADO();
$OPROCESO = new OPROCESO();

$IDOP = "";
$ACCION = "";
$NUMERO_OPROCESO = "";
$FECHA_TERMINO_ESTIMADA = "";
$CANTIDAD_CAJA = "";
$OBSERVACION_OPROCESO = "";
$MENSAJE = "";
$TIPOMENSAJE = "success";

$MERCADOS_SEL = array();
$PRODUCTORES_SEL = array();
$VARIEDADES_SEL = array();
$ESTANDARES_SEL = array();

$ARRAYMERCADO = $MERCADO_ADO->listarMercadoPorEmpresaCBX($EMPRESAS);
$ARRAYPRODUCTOR = $PRODUCTOR_ADO->listarProductorPorEmpresaCBX($EMPRESAS);
$ARRAYVESPECIES = $VESPECIES_ADO->listarVespeciesPorEmpresaCBX($EMPRESAS);
$ARRAYESTANDAR = $EEXPORTACION_ADO->listarEstandarPorEmpresaCBX($EMPRESAS);
$ARRAYRMERCADO = $RMERCADO_ADO->listarRmercadoPorEmpresaCBX($EMPRESAS);

$LISTAMERCADOS = array();
$LISTAPRODUCTORES = array();
$LISTAVARIEDADES = array();
$LISTAESTANDARES = array();
$MERCADOSPRODUCTOR = array();
$MERCADOSESTANDAR = array();

if ($ARRAYMERCADO) {
    foreach ($ARRAYMERCADO as $r) {
        $LISTAMERCADOS[(int)$r['ID_MERCADO']] = $r['NOMBRE_MERCADO'];
    }
}
if ($ARRAYPRODUCTOR) {
    foreach ($ARRAYPRODUCTOR as $r) {
        $LISTAPRODUCTORES[(int)$r['ID_PRODUCTOR']] = $r['CSG_PRODUCTOR'] . ' : ' . $r['NOMBRE_PRODUCTOR'];
    }
}
if ($ARRAYVESPECIES) {
    foreach ($ARRAYVESPECIES as $r) {
        $LISTAVARIEDADES[(int)$r['ID_VESPECIES']] = $r['NOMBRE_VESPECIES'];
    }
}
if ($ARRAYESTANDAR) {
    foreach ($ARRAYESTANDAR as $r) {
        $IDEST = (int)$r['ID_ESTANDAR'];
        $LISTAESTANDARES[$IDEST] = $r['CODIGO_ESTANDAR'] . ' : ' . $r['NOMBRE_ESTANDAR'];
        $MERCADOSESTANDAR[$IDEST] = array();
        $ARRAYREX = $REEXPORTACIONMERCADO_ADO->buscarPorEstandar($IDEST);
        if ($ARRAYREX) {
            foreach ($ARRAYREX as $rx) {
                $MERCADOSESTANDAR[$IDEST][] = (int)$rx['ID_MERCADO'];
            }
            $MERCADOSESTANDAR[$IDEST] = array_values(array_unique($MERCADOSESTANDAR[$IDEST]));
        }
    }
}
if ($ARRAYRMERCADO) {
    foreach ($ARRAYRMERCADO as $r) {
        $IDPROD = (int)$r['ID_PRODUCTOR'];
        $IDMER = (int)$r['ID_MERCADO'];
        if (!isset($MERCADOSPRODUCTOR[$IDPROD])) {
            $MERCADOSPRODUCTOR[$IDPROD] = array();
        }
        if (!in_array($IDMER, $MERCADOSPRODUCTOR[$IDPROD])) {
            $MERCADOSPRODUCTOR[$IDPROD][] = $IDMER;
        }
    }
}

function validarCompatibilidadMercadosOP($MERCADOS_SEL, $PRODUCTORES_SEL, $ESTANDARES_SEL, $MERCADOSPRODUCTOR, $MERCADOSESTANDAR, $LISTAPRODUCTORES, $LISTAESTANDARES, $LISTAMERCADOS)
{
    $MERCADOS_SEL = array_values(array_unique(array_filter(array_map('intval', (array)$MERCADOS_SEL))));
    $PRODUCTORES_SEL = array_values(array_unique(array_filter(array_map('intval', (array)$PRODUCTORES_SEL))));
    $ESTANDARES_SEL = array_values(array_unique(array_filter(array_map('intval', (array)$ESTANDARES_SEL))));

    if (count($MERCADOS_SEL) == 0) {
        return array(true, "");
    }

    $ERRORES = array();

    foreach ($PRODUCTORES_SEL as $IDPROD) {
        $MERCADOSPROD = isset($MERCADOSPRODUCTOR[$IDPROD]) ? $MERCADOSPRODUCTOR[$IDPROD] : array();
        $FALTAN = array_values(array_diff($MERCADOS_SEL, $MERCADOSPROD));
        if (count($FALTAN) > 0) {
            $NOMBRES = array();
            foreach ($FALTAN as $idm) {
                if (isset($LISTAMERCADOS[$idm])) {
                    $NOMBRES[] = $LISTAMERCADOS[$idm];
                }
            }
            $ERRORES[] = "Productor <strong>" . (isset($LISTAPRODUCTORES[$IDPROD]) ? $LISTAPRODUCTORES[$IDPROD] : $IDPROD) . "</strong> no habilitado en: " . implode(', ', $NOMBRES);
        }
    }

    foreach ($ESTANDARES_SEL as $IDEST) {
        $MERCEST = isset($MERCADOSESTANDAR[$IDEST]) ? $MERCADOSESTANDAR[$IDEST] : array();
        $FALTAN = array_values(array_diff($MERCADOS_SEL, $MERCEST));
        if (count($FALTAN) > 0) {
            $NOMBRES = array();
            foreach ($FALTAN as $idm) {
                if (isset($LISTAMERCADOS[$idm])) {
                    $NOMBRES[] = $LISTAMERCADOS[$idm];
                }
            }
            $ERRORES[] = "Estándar <strong>" . (isset($LISTAESTANDARES[$IDEST]) ? $LISTAESTANDARES[$IDEST] : $IDEST) . "</strong> no habilitado en: " . implode(', ', $NOMBRES);
        }
    }

    if (count($ERRORES) > 0) {
        return array(false, implode("<br>", $ERRORES));
    }

    return array(true, "");
}


function limpiarTextoOp($VALOR)
{
    return strtoupper(trim((string)$VALOR));
}

function obtenerResumenDetalleOp($MERCADOS_SEL, $PRODUCTORES_SEL, $VARIEDADES_SEL, $ESTANDARES_SEL)
{
    return array(
        'MERCADOS' => count(array_filter(array_unique(array_map('intval', (array)$MERCADOS_SEL)))),
        'PRODUCTORES' => count(array_filter(array_unique(array_map('intval', (array)$PRODUCTORES_SEL)))),
        'VARIEDADES' => count(array_filter(array_unique(array_map('intval', (array)$VARIEDADES_SEL)))),
        'ESTANDARES' => count(array_filter(array_unique(array_map('intval', (array)$ESTANDARES_SEL))))
    );
}

function obtenerEstadoOrdenProcesoOp($ESTADOREGISTRO)
{
    $ESTADO = (int)$ESTADOREGISTRO;
    if ($ESTADO === 2) {
        return array('texto' => 'Completada', 'clase' => 'estado-op-completada');
    }
    if ($ESTADO === 0) {
        return array('texto' => 'Deshabilitada', 'clase' => 'estado-op-deshabilitada');
    }
    return array('texto' => 'Activa', 'clase' => 'estado-op-activa');
}

if (isset($_GET['id']) && isset($_GET['a'])) {
    $IDOP = (int)$_GET['id'];
    $ACCION = $_GET['a'];
    if ($ACCION == 'editar' && $IDOP > 0) {
        $ARRAYOPID = $OPROCESO_ADO->verOrdenProceso($IDOP);
        if ($ARRAYOPID && $ACCION == 'editar') {
            $NUMERO_OPROCESO = $ARRAYOPID[0]['NUMERO_OPROCESO'];
            $FECHA_TERMINO_ESTIMADA = $ARRAYOPID[0]['FECHA_TERMINO_ESTIMADA'];
            $CANTIDAD_CAJA = $ARRAYOPID[0]['CANTIDAD_CAJA'];
            $OBSERVACION_OPROCESO = $ARRAYOPID[0]['OBSERVACION_OPROCESO'];

            $ARRAYM = $OPROCESO_ADO->listarMercadosPorOp($IDOP);
            foreach ($ARRAYM as $r) {
                $MERCADOS_SEL[] = (int)$r['ID_MERCADO'];
            }

            $ARRAYPV = $OPROCESO_ADO->listarProductorVariedadPorOp($IDOP);
            foreach ($ARRAYPV as $r) {
                $PRODUCTORES_SEL[] = (int)$r['ID_PRODUCTOR'];
                $VARIEDADES_SEL[] = (int)$r['ID_VESPECIES'];
            }
            $PRODUCTORES_SEL = array_values(array_unique($PRODUCTORES_SEL));
            $VARIEDADES_SEL = array_values(array_unique($VARIEDADES_SEL));

            $ARRAYE = $OPROCESO_ADO->listarEstandarPorOp($IDOP);
            foreach ($ARRAYE as $r) {
                $ESTANDARES_SEL[] = (int)$r['ID_ESTANDAR'];
            }
            $ESTANDARES_SEL = array_values(array_unique($ESTANDARES_SEL));
        }
    }
}

if (isset($_POST['CREAR'])) {
    $NUMERO_OPROCESO = limpiarTextoOp($_POST['NUMERO_OPROCESO']);
    $FECHA_TERMINO_ESTIMADA = $_POST['FECHA_TERMINO_ESTIMADA'];
    $CANTIDAD_CAJA = $_POST['CANTIDAD_CAJA'];
    $OBSERVACION_OPROCESO = $_POST['OBSERVACION_OPROCESO'];

    $MERCADOS_SEL = isset($_POST['MERCADOS']) ? array_values(array_unique(array_map('intval', $_POST['MERCADOS']))) : array();
    $PRODUCTORES_SEL = isset($_POST['PRODUCTORES']) ? array_values(array_unique(array_map('intval', $_POST['PRODUCTORES']))) : array();
    $VARIEDADES_SEL = isset($_POST['VARIEDADES']) ? array_values(array_unique(array_map('intval', $_POST['VARIEDADES']))) : array();
    $ESTANDARES_SEL = isset($_POST['ESTANDARES']) ? array_values(array_unique(array_map('intval', $_POST['ESTANDARES']))) : array();

    list($VALIDO, $DETALLEERROR) = validarCompatibilidadMercadosOP(
        $MERCADOS_SEL,
        $PRODUCTORES_SEL,
        $ESTANDARES_SEL,
        $MERCADOSPRODUCTOR,
        $MERCADOSESTANDAR,
        $LISTAPRODUCTORES,
        $LISTAESTANDARES,
        $LISTAMERCADOS
    );

    if ($NUMERO_OPROCESO == "") {
        $MENSAJE = "Debe ingresar un número de OP válido.";
        $TIPOMENSAJE = "warning";
    } else if (count($MERCADOS_SEL) == 0 || count($PRODUCTORES_SEL) == 0 || count($VARIEDADES_SEL) == 0 || count($ESTANDARES_SEL) == 0) {
        $MENSAJE = "Para generar la OP completa debe seleccionar al menos un mercado, productor, variedad y estándar.";
        $TIPOMENSAJE = "warning";
    } else if ($OPROCESO_ADO->existeNumeroOpEmpresa($EMPRESAS, $NUMERO_OPROCESO, 0)) {
        $MENSAJE = "El número de OP <strong>" . $NUMERO_OPROCESO . "</strong> ya existe, utilice uno distinto.";
        $TIPOMENSAJE = "warning";
    } else if (!$VALIDO) {
        $MENSAJE = "Validación de mercados no cumplida:<br>" . $DETALLEERROR;
        $TIPOMENSAJE = "warning";
    } else {
        $OPROCESO->__SET('NUMERO_OPROCESO', $NUMERO_OPROCESO);
        $OPROCESO->__SET('FECHA_TERMINO_ESTIMADA', $FECHA_TERMINO_ESTIMADA);
        $OPROCESO->__SET('CANTIDAD_CAJA', $CANTIDAD_CAJA);
        $OPROCESO->__SET('OBSERVACION_OPROCESO', $OBSERVACION_OPROCESO);
        $OPROCESO->__SET('ID_EMPRESA', $EMPRESAS);
        $OPROCESO->__SET('ID_USUARIOI', $IDUSUARIOS);
        $OPROCESO->__SET('ID_USUARIOM', $IDUSUARIOS);

        $IDNUEVAOP = $OPROCESO_ADO->agregarOrdenProceso($OPROCESO);

        foreach ($MERCADOS_SEL as $idMercado) {
            if ((int)$idMercado > 0) {
                $OPROCESO_ADO->agregarMercado($IDNUEVAOP, (int)$idMercado);
            }
        }

        foreach ($PRODUCTORES_SEL as $idProductor) {
            foreach ($VARIEDADES_SEL as $idVariedad) {
                if ((int)$idProductor > 0 && (int)$idVariedad > 0) {
                    $OPROCESO_ADO->agregarProductorVariedad($IDNUEVAOP, (int)$idProductor, (int)$idVariedad);
                }
            }
        }

        foreach ($ESTANDARES_SEL as $idEstandar) {
            if ((int)$idEstandar > 0) {
                $OPROCESO_ADO->agregarEstandar($IDNUEVAOP, (int)$idEstandar);
            }
        }

        $RESUMENDETALLE = obtenerResumenDetalleOp($MERCADOS_SEL, $PRODUCTORES_SEL, $VARIEDADES_SEL, $ESTANDARES_SEL);
        $MENSAJE = "OP creada correctamente.<br>Detalle generado: "
            . $RESUMENDETALLE['MERCADOS'] . " mercados, "
            . $RESUMENDETALLE['PRODUCTORES'] . " productores, "
            . $RESUMENDETALLE['VARIEDADES'] . " variedades y "
            . $RESUMENDETALLE['ESTANDARES'] . " estándares.";
        $TIPOMENSAJE = "success";

        $NUMERO_OPROCESO = "";
        $FECHA_TERMINO_ESTIMADA = "";
        $CANTIDAD_CAJA = "";
        $OBSERVACION_OPROCESO = "";
        $MERCADOS_SEL = array();
        $PRODUCTORES_SEL = array();
        $VARIEDADES_SEL = array();
        $ESTANDARES_SEL = array();
    }
}

if (isset($_POST['GUARDAR'])) {
    $IDOPPOST = (int)$_POST['ID_OPROCESO'];
    $IDOP = $IDOPPOST;
    $ACCION = 'editar';

    $NUMERO_OPROCESO = limpiarTextoOp($_POST['NUMERO_OPROCESO']);
    $FECHA_TERMINO_ESTIMADA = $_POST['FECHA_TERMINO_ESTIMADA'];
    $CANTIDAD_CAJA = $_POST['CANTIDAD_CAJA'];
    $OBSERVACION_OPROCESO = $_POST['OBSERVACION_OPROCESO'];

    $MERCADOS_SEL = isset($_POST['MERCADOS']) ? array_values(array_unique(array_map('intval', $_POST['MERCADOS']))) : array();
    $PRODUCTORES_SEL = isset($_POST['PRODUCTORES']) ? array_values(array_unique(array_map('intval', $_POST['PRODUCTORES']))) : array();
    $VARIEDADES_SEL = isset($_POST['VARIEDADES']) ? array_values(array_unique(array_map('intval', $_POST['VARIEDADES']))) : array();
    $ESTANDARES_SEL = isset($_POST['ESTANDARES']) ? array_values(array_unique(array_map('intval', $_POST['ESTANDARES']))) : array();

    list($VALIDO, $DETALLEERROR) = validarCompatibilidadMercadosOP(
        $MERCADOS_SEL,
        $PRODUCTORES_SEL,
        $ESTANDARES_SEL,
        $MERCADOSPRODUCTOR,
        $MERCADOSESTANDAR,
        $LISTAPRODUCTORES,
        $LISTAESTANDARES,
        $LISTAMERCADOS
    );

    if ($NUMERO_OPROCESO == "") {
        $MENSAJE = "Debe ingresar un número de OP válido.";
        $TIPOMENSAJE = "warning";
    } else if (count($MERCADOS_SEL) == 0 || count($PRODUCTORES_SEL) == 0 || count($VARIEDADES_SEL) == 0 || count($ESTANDARES_SEL) == 0) {
        $MENSAJE = "Para actualizar la OP completa debe seleccionar al menos un mercado, productor, variedad y estándar.";
        $TIPOMENSAJE = "warning";
    } else if ($OPROCESO_ADO->existeNumeroOpEmpresa($EMPRESAS, $NUMERO_OPROCESO, $IDOPPOST)) {
        $MENSAJE = "El número de OP <strong>" . $NUMERO_OPROCESO . "</strong> ya existe, utilice uno distinto.";
        $TIPOMENSAJE = "warning";
    } else if (!$VALIDO) {
        $MENSAJE = "Validación de mercados no cumplida:<br>" . $DETALLEERROR;
        $TIPOMENSAJE = "warning";
    } else if ($IDOPPOST > 0) {
        $OPROCESO->__SET('ID_OPROCESO', $IDOPPOST);
        $OPROCESO->__SET('NUMERO_OPROCESO', $NUMERO_OPROCESO);
        $OPROCESO->__SET('FECHA_TERMINO_ESTIMADA', $FECHA_TERMINO_ESTIMADA);
        $OPROCESO->__SET('CANTIDAD_CAJA', $CANTIDAD_CAJA);
        $OPROCESO->__SET('OBSERVACION_OPROCESO', $OBSERVACION_OPROCESO);
        $OPROCESO->__SET('ID_USUARIOM', $IDUSUARIOS);

        $OPROCESO_ADO->actualizarOrdenProceso($OPROCESO);
        $OPROCESO_ADO->limpiarDetalleOrdenProceso($IDOPPOST);

        foreach ($MERCADOS_SEL as $idMercado) {
            if ((int)$idMercado > 0) {
                $OPROCESO_ADO->agregarMercado($IDOPPOST, (int)$idMercado);
            }
        }

        foreach ($PRODUCTORES_SEL as $idProductor) {
            foreach ($VARIEDADES_SEL as $idVariedad) {
                if ((int)$idProductor > 0 && (int)$idVariedad > 0) {
                    $OPROCESO_ADO->agregarProductorVariedad($IDOPPOST, (int)$idProductor, (int)$idVariedad);
                }
            }
        }

        foreach ($ESTANDARES_SEL as $idEstandar) {
            if ((int)$idEstandar > 0) {
                $OPROCESO_ADO->agregarEstandar($IDOPPOST, (int)$idEstandar);
            }
        }

        $RESUMENDETALLE = obtenerResumenDetalleOp($MERCADOS_SEL, $PRODUCTORES_SEL, $VARIEDADES_SEL, $ESTANDARES_SEL);
        $MENSAJE = "OP actualizada correctamente.<br>Detalle generado: "
            . $RESUMENDETALLE['MERCADOS'] . " mercados, "
            . $RESUMENDETALLE['PRODUCTORES'] . " productores, "
            . $RESUMENDETALLE['VARIEDADES'] . " variedades y "
            . $RESUMENDETALLE['ESTANDARES'] . " estándares.";
        $TIPOMENSAJE = "success";
        $ACCION = "";
        $IDOP = "";
        $NUMERO_OPROCESO = "";
        $FECHA_TERMINO_ESTIMADA = "";
        $CANTIDAD_CAJA = "";
        $OBSERVACION_OPROCESO = "";
        $MERCADOS_SEL = array();
        $PRODUCTORES_SEL = array();
        $VARIEDADES_SEL = array();
        $ESTANDARES_SEL = array();
    }
}

if (isset($_POST['COMPLETAR'])) {
    $IDACCION = (int)$_POST['ID_OPROCESO'];
    if ($IDACCION > 0) {
        $OPROCESO_ADO->marcarCompletadaOrdenProceso($IDACCION, $IDUSUARIOS);
        $MENSAJE = "OP marcada como completada correctamente.";
        $TIPOMENSAJE = "success";
    }
}

if (isset($_POST['DESHABILITAR'])) {
    $IDACCION = (int)$_POST['ID_OPROCESO'];
    if ($IDACCION > 0) {
        $OPROCESO_ADO->marcarDeshabilitadaOrdenProceso($IDACCION, $IDUSUARIOS);
        $MENSAJE = "OP marcada como deshabilitada correctamente.";
        $TIPOMENSAJE = "success";
    }
}

$SUGERIDO_NUMERO_OP = $OPROCESO_ADO->obtenerSiguienteNumeroOpEmpresa($EMPRESAS);
$ARRAYOP = $OPROCESO_ADO->listarOrdenProcesoPorEmpresa($EMPRESAS);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ordenes de Proceso</title>
    <?php include_once "../../assest/config/urlHead.php"; ?>
    <style>
        /* sistemRR */
        #form_op .form-control,
        #form_op .select2-container--default .select2-selection--single,
        #form_op .select2-container--default .select2-selection--multiple {
            border-radius: 4px !important;
            min-height: 29px !important;
            font-size: 12px;
        }

        #form_op .select2-container--default .select2-selection--multiple {
            min-height: 29px !important;
        }

        #form_op .select2-container--default .select2-selection--multiple .select2-selection__choice {
            border-radius: 4px !important;
            min-height: 22px;
            line-height: 18px;
            padding: 2px 8px;
        }


        .content .box > .box-header.with-border {
            background: #2383e2;
            border-bottom: 0;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
            padding-top: 0.95rem;
            padding-bottom: 0.95rem;
        }

        .content .box > .box-header.with-border .box-title {
            color: #ffffff;
            font-weight: 300;
            font-size: 16px;
        }

        #listar_op {
            border-collapse: separate;
            border-spacing: 0 8px;
            min-width: 1180px;
        }

        #listar_op thead th {
            border: 0;
            background: #f4f6fb;
            color: #475467;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .4px;
            white-space: nowrap;
            padding: 0.55rem 0.6rem;
        }

        #listar_op tbody td {
            background: #fff;
            border-top: 1px solid #edf0f5;
            border-bottom: 1px solid #edf0f5;
            padding: 0.5rem 0.6rem;
            vertical-align: middle;
            font-size: 12px;
        }

        #listar_op tbody tr td:first-child {
            border-left: 1px solid #edf0f5;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        #listar_op tbody tr td:last-child {
            border-right: 1px solid #edf0f5;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        #listar_op tbody tr:hover td {
            background-color: #f9fbff;
        }

        .op-chip {
            display: inline-block;
            min-width: 28px;
            text-align: center;
            border-radius: 10px;
            padding: 0.1rem 0.35rem;
            font-size: 11px;
            font-weight: 600;
            color: #155eef;
            background: #eef4ff;
        }

        .op-group-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            max-width: 280px;
        }

        .op-group-item {
            display: inline-block;
            border-radius: 999px;
            padding: 0.18rem 0.55rem;
            font-size: 10px;
            font-weight: 600;
            line-height: 1.2;
            background: #f4f6fb;
            color: #344054;
            border: 1px solid #e4e7ec;
            white-space: nowrap;
        }

        .op-proceso-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            max-width: 320px;
        }

        .op-proceso-card {
            background: #f5f8ff;
            border: 1px solid #d9e6ff;
            border-radius: 10px;
            padding: 5px 8px;
            line-height: 1.2;
        }

        .op-proceso-card .n {
            font-weight: 700;
            color: #1d3f72;
            font-size: 11px;
            background: #dbeafe;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 2px 6px;
            margin-bottom: 4px;
        }

        .estado-op-badge {
            display: inline-block;
            border-radius: 999px;
            padding: 0.2rem 0.55rem;
            font-size: 10px;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .estado-op-activa {
            color: #05603a;
            background: #ecfdf3;
            border-color: #abefc6;
        }

        .estado-op-completada {
            color: #194185;
            background: #eff8ff;
            border-color: #b2ddff;
        }

        .estado-op-deshabilitada {
            color: #912018;
            background: #fef3f2;
            border-color: #fecdca;
        }

        .op-proceso-card .f {
            color: #4b5565;
            font-size: 10px;
        }
    </style>
</head>
<body class="exportadora-form-compact hold-transition light-skin fixed sidebar-mini theme-primary sistemRR">
<div class="wrapper">
    <?php include_once "../../assest/config/menuExpo.php"; ?>
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">
                        <h3 class="page-title">Ordenes de Proceso (OP)</h3>
                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                                    <li class="breadcrumb-item" aria-current="page">Producción</li>
                                    <li class="breadcrumb-item active" aria-current="page"><a href="#">Ordenes de Proceso</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <?php include_once "../../assest/config/verIndicadorEconomico.php"; ?>
                </div>
            </div>
            <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title"><?php echo ($ACCION == 'editar') ? 'Editar OP' : 'Registrar OP'; ?></h4>
                </div>
                <div class="box-body">
                    <form class="form-one-line" data-form-layout="oneline-1" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.65rem;padding:0.65rem 0;border-bottom:1px solid #3a3a3a;" method="post" id="form_op">
                        <input type="hidden" name="ID_OPROCESO" value="<?php echo $IDOP; ?>">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Número OP</label>
                                <input class="form-control" name="NUMERO_OPROCESO" value="<?php echo $NUMERO_OPROCESO; ?>" placeholder="<?php echo $SUGERIDO_NUMERO_OP; ?>" style="text-transform:uppercase;" required>
                                <small class="text-muted">Sugerido: <?php echo $SUGERIDO_NUMERO_OP; ?></small>
                            </div>
                            <div class="col-md-3">
                                <label>Cantidad de cajas</label>
                                <input type="number" class="form-control" name="CANTIDAD_CAJA" min="1" value="<?php echo $CANTIDAD_CAJA; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label>Fecha estimada de termino</label>
                                <input type="date" class="form-control" name="FECHA_TERMINO_ESTIMADA" value="<?php echo $FECHA_TERMINO_ESTIMADA; ?>" required>
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Mercados</label>
                                <select class="form-control select2" name="MERCADOS[]" multiple required>
                                    <?php foreach ($ARRAYMERCADO as $r) { ?>
                                        <option value="<?php echo $r['ID_MERCADO']; ?>" <?php echo in_array((int)$r['ID_MERCADO'], $MERCADOS_SEL) ? 'selected' : ''; ?>>
                                            <?php echo $r['NOMBRE_MERCADO']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Productor</label>
                                <select class="form-control select2" name="PRODUCTORES[]" multiple required>
                                    <?php foreach ($ARRAYPRODUCTOR as $r) { ?>
                                        <option value="<?php echo $r['ID_PRODUCTOR']; ?>" <?php echo in_array((int)$r['ID_PRODUCTOR'], $PRODUCTORES_SEL) ? 'selected' : ''; ?>>
                                            <?php echo $r['CSG_PRODUCTOR'] . ' : ' . $r['NOMBRE_PRODUCTOR']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Variedad por productor</label>
                                <select class="form-control select2" name="VARIEDADES[]" multiple required>
                                    <?php foreach ($ARRAYVESPECIES as $r) { ?>
                                        <option value="<?php echo $r['ID_VESPECIES']; ?>" <?php echo in_array((int)$r['ID_VESPECIES'], $VARIEDADES_SEL) ? 'selected' : ''; ?>>
                                            <?php echo $r['NOMBRE_VESPECIES']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Estándar</label>
                                <select class="form-control select2" name="ESTANDARES[]" multiple required>
                                    <?php foreach ($ARRAYESTANDAR as $r) { ?>
                                        <option value="<?php echo $r['ID_ESTANDAR']; ?>" <?php echo in_array((int)$r['ID_ESTANDAR'], $ESTANDARES_SEL) ? 'selected' : ''; ?>>
                                            <?php echo $r['CODIGO_ESTANDAR'] . ' : ' . $r['NOMBRE_ESTANDAR']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Observación</label>
                                <textarea class="form-control" name="OBSERVACION_OPROCESO"><?php echo $OBSERVACION_OPROCESO; ?></textarea>
                            </div>
                        </div><br>
                        <?php if ($ACCION == 'editar') { ?>
                            <button class="btn btn-warning" name="GUARDAR" value="GUARDAR">Guardar cambios</button>
                            <a href="registroOrdenProceso.php" class="btn btn-secondary">Cancelar</a>
                        <?php } else { ?>
                            <button class="btn btn-primary" name="CREAR" value="CREAR">Guardar OP</button>
                        <?php } ?>
                    </form>
                </div>
            </div>

            <div class="box">
                <div class="box-header with-border"><h4 class="box-title">OP Registradas</h4></div>
                <div class="box-body table-responsive">
                    <table id="listar_op" class="table-hover" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Número</th>
                                <th>Cajas</th>
                                <th>Fecha término</th>
                                <th>Estado</th>
                                <th>Mercados</th>
                                <th>Productores</th>
                                <th>Variedades</th>
                                <th>Estándares</th>
                                <th>Procesos asociados</th>
                                <th style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($ARRAYOP as $r) { ?>
                            <tr>
                                <td><?php echo $r['ID_OPROCESO']; ?></td>
                                <td><?php echo $r['NUMERO_OPROCESO']; ?></td>
                                <td><?php echo $r['CANTIDAD_CAJA']; ?></td>
                                <td><?php echo $r['FECHA_TERMINO_ESTIMADA']; ?></td>
                                <?php $ESTADOOP = obtenerEstadoOrdenProcesoOp($r['ESTADO_REGISTRO']); ?>
                                <td><span class="estado-op-badge <?php echo $ESTADOOP['clase']; ?>"><?php echo $ESTADOOP['texto']; ?></span></td>
                                <?php
                                $IDOPLISTA = (int)$r['ID_OPROCESO'];
                                $ARRAYMDET = $OPROCESO_ADO->listarMercadosPorOp($IDOPLISTA);
                                $NOMBRESMERCADOSDET = array();
                                foreach ($ARRAYMDET as $dtm) {
                                    $IDMERCADODET = (int)$dtm['ID_MERCADO'];
                                    if (isset($LISTAMERCADOS[$IDMERCADODET])) {
                                        $NOMBRESMERCADOSDET[$IDMERCADODET] = $LISTAMERCADOS[$IDMERCADODET];
                                    }
                                }
                                $ARRAYPVDET = $OPROCESO_ADO->listarProductorVariedadPorOp($IDOPLISTA);
                                $NOMBRESPRODUCTORESDET = array();
                                $NOMBRESVARIEDADESDET = array();
                                foreach ($ARRAYPVDET as $dt) {
                                    $IDPRODUCTORDET = (int)$dt['ID_PRODUCTOR'];
                                    if (isset($LISTAPRODUCTORES[$IDPRODUCTORDET])) {
                                        $NOMBRESPRODUCTORESDET[$IDPRODUCTORDET] = $LISTAPRODUCTORES[$IDPRODUCTORDET];
                                    }
                                    $IDVARIEDADDET = (int)$dt['ID_VESPECIES'];
                                    if (isset($LISTAVARIEDADES[$IDVARIEDADDET])) {
                                        $NOMBRESVARIEDADESDET[$IDVARIEDADDET] = $LISTAVARIEDADES[$IDVARIEDADDET];
                                    }
                                }
                                $ARRAYEDET = $OPROCESO_ADO->listarEstandarPorOp($IDOPLISTA);
                                $NOMBRESESTANDARESDET = array();
                                foreach ($ARRAYEDET as $dte) {
                                    $IDESTANDARDET = (int)$dte['ID_ESTANDAR'];
                                    if (isset($LISTAESTANDARES[$IDESTANDARDET])) {
                                        $NOMBRESESTANDARESDET[$IDESTANDARDET] = $LISTAESTANDARES[$IDESTANDARDET];
                                    }
                                }
                                $ARRAYPROCESOSOP = $OPROCESO_ADO->listarProcesosAsociados($IDOPLISTA);
                                $TOTALPROCESOSASOCIADOS = count($ARRAYPROCESOSOP);
                                ?>
                                <td>
                                    <div class="op-group-list">
                                    <?php if ($NOMBRESMERCADOSDET) { ?>
                                        <?php foreach ($NOMBRESMERCADOSDET as $nomMercado) { ?>
                                            <span class="op-group-item"><?php echo $nomMercado; ?></span>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <small class="text-muted">Sin mercado</small>
                                    <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="op-group-list">
                                    <?php if ($NOMBRESPRODUCTORESDET) { ?>
                                        <?php foreach ($NOMBRESPRODUCTORESDET as $nomProductor) { ?>
                                            <span class="op-group-item"><?php echo $nomProductor; ?></span>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <small class="text-muted">Sin productor</small>
                                    <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="op-group-list">
                                    <?php if ($NOMBRESVARIEDADESDET) { ?>
                                        <?php foreach ($NOMBRESVARIEDADESDET as $nomVariedad) { ?>
                                            <span class="op-group-item"><?php echo $nomVariedad; ?></span>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <small class="text-muted">Sin variedad</small>
                                    <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="op-group-list">
                                    <?php if ($NOMBRESESTANDARESDET) { ?>
                                        <?php foreach ($NOMBRESESTANDARESDET as $nomEstandar) { ?>
                                            <span class="op-group-item"><?php echo $nomEstandar; ?></span>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <small class="text-muted">Sin estándar</small>
                                    <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="op-proceso-cards">
                                        <?php if ($ARRAYPROCESOSOP) { ?>
                                            <?php foreach ($ARRAYPROCESOSOP as $rp) { ?>
                                                <div class="op-proceso-card">
                                                    <div class="n">P-<?php echo $rp['NUMERO_PROCESO']; ?></div>
                                                    <div class="f"><?php echo $rp['FECHA_PROCESO']; ?></div>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <small class="text-muted">Sin procesos asociados</small>
                                        <?php } ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <?php if ((int)$r['ESTADO_REGISTRO'] === 1) { ?>
                                                <a class="dropdown-item" href="registroOrdenProceso.php?id=<?php echo $r['ID_OPROCESO']; ?>&a=editar">Editar</a>
                                                <form class="form-one-line" data-form-layout="oneline-2" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.65rem;padding:0.65rem 0;border-bottom:1px solid #3a3a3a;" method="post" onsubmit="return confirm('¿Marcar esta OP como completada?');">
                                                    <input type="hidden" name="ID_OPROCESO" value="<?php echo $r['ID_OPROCESO']; ?>">
                                                    <button class="dropdown-item text-primary" name="COMPLETAR" value="COMPLETAR">Completada</button>
                                                </form>
                                                <form class="form-one-line" data-form-layout="oneline-3" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.65rem;padding:0.65rem 0;border-bottom:1px solid #3a3a3a;" method="post" onsubmit="return confirm('¿Marcar esta OP como deshabilitada?');">
                                                    <input type="hidden" name="ID_OPROCESO" value="<?php echo $r['ID_OPROCESO']; ?>">
                                                    <button class="dropdown-item text-danger" name="DESHABILITAR" value="DESHABILITAR">Deshabilitada</button>
                                                </form>
                                            <?php } else { ?>
                                                <span class="dropdown-item text-muted">Sin acciones disponibles</span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        </div>
    </div>
    <?php include_once "../../assest/config/menuExtraExpo.php"; ?>
</div>
<?php include_once "../../assest/config/urlBase.php"; ?>
<script>
    (function () {
        var form = document.getElementById('form_op');
        if (!form) return;
        var inputNumero = form.querySelector('input[name="NUMERO_OPROCESO"]');
        if (inputNumero) {
            inputNumero.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        }

        form.addEventListener('submit', function (e) {
            var mercados = form.querySelector('select[name="MERCADOS[]"]');
            var productores = form.querySelector('select[name="PRODUCTORES[]"]');
            var variedades = form.querySelector('select[name="VARIEDADES[]"]');
            var estandares = form.querySelector('select[name="ESTANDARES[]"]');

            var validarMulti = function (el) { return el && el.selectedOptions && el.selectedOptions.length > 0; };

            if (!validarMulti(mercados) || !validarMulti(productores) || !validarMulti(variedades) || !validarMulti(estandares)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Ordenes de Proceso',
                    html: 'Debe seleccionar al menos un mercado, productor, variedad y estándar para una OP completa.',
                    confirmButtonText: 'Entendido'
                });
            }
        });
    })();
</script>
<?php if ($MENSAJE != "") { ?>
<script>
    Swal.fire({
        icon: '<?php echo $TIPOMENSAJE; ?>',
        title: 'Ordenes de Proceso',
        html: '<?php echo $MENSAJE; ?>',
        confirmButtonText: 'Entendido'
    });
</script>
<?php } ?>
</body>
</html>
