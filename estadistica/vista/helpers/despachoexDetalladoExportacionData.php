<?php

include_once "../../assest/config/validarUsuarioOpera.php";

include_once '../../assest/controlador/ESPECIES_ADO.php';
include_once '../../assest/controlador/VESPECIES_ADO.php';
include_once '../../assest/controlador/PRODUCTOR_ADO.php';
include_once '../../assest/controlador/TMANEJO_ADO.php';
include_once '../../assest/controlador/TCALIBRE_ADO.php';
include_once '../../assest/controlador/TEMBALAJE_ADO.php';

include_once '../../assest/controlador/CONDUCTOR_ADO.php';
include_once '../../assest/controlador/TRANSPORTE_ADO.php';
include_once '../../assest/controlador/COMPRADOR_ADO.php';

include_once '../../assest/controlador/TPROCESO_ADO.php';
include_once '../../assest/controlador/TREEMBALAJE_ADO.php';
include_once '../../assest/controlador/PROCESO_ADO.php';
include_once '../../assest/controlador/REEMBALAJE_ADO.php';

include_once '../../assest/controlador/EEXPORTACION_ADO.php';
include_once '../../assest/controlador/EINDUSTRIAL_ADO.php';
include_once '../../assest/controlador/ERECEPCION_ADO.php';

include_once '../../assest/controlador/EXIMATERIAPRIMA_ADO.php';
include_once '../../assest/controlador/RECEPCIONMP_ADO.php';
include_once '../../assest/controlador/DESPACHOMP_ADO.php';
include_once '../../assest/controlador/EXIINDUSTRIAL_ADO.php';
include_once '../../assest/controlador/RECEPCIONIND_ADO.php';
include_once '../../assest/controlador/DESPACHOIND_ADO.php';
include_once '../../assest/controlador/EXIEXPORTACION_ADO.php';
include_once '../../assest/controlador/RECEPCIONPT_ADO.php';
include_once '../../assest/controlador/DESPACHOPT_ADO.php';
include_once '../../assest/controlador/DESPACHOEX_ADO.php';
include_once '../../assest/controlador/REPALETIZAJEEX_ADO.php';

include_once '../../assest/controlador/EMPRESA_ADO.php';
include_once '../../assest/controlador/PLANTA_ADO.php';
include_once '../../assest/controlador/TEMPORADA_ADO.php';

include_once '../../assest/controlador/ICARGA_ADO.php';
include_once '../../assest/controlador/DFINAL_ADO.php';
include_once '../../assest/controlador/RFINAL_ADO.php';
include_once '../../assest/controlador/BROKER_ADO.php';
include_once '../../assest/controlador/MERCADO_ADO.php';
include_once '../../assest/controlador/PAIS_ADO.php';

include_once '../../assest/controlador/LDESTINO_ADO.php';
include_once '../../assest/controlador/ADESTINO_ADO.php';
include_once '../../assest/controlador/PDESTINO_ADO.php';

function indexarPorClave($rows, $key)
{
    $map = [];
    if (!$rows || !is_array($rows)) {
        return $map;
    }
    foreach ($rows as $row) {
        if (is_array($row) && isset($row[$key])) {
            $map[$row[$key]] = $row;
        }
    }
    return $map;
}

function obtenerCacheDespachoexDetalladoExportacion($key, $ttl)
{
    $ruta = sys_get_temp_dir() . '/detallado_exportacion_' . md5($key) . '.cache';
    if (!file_exists($ruta)) {
        return null;
    }
    if ((time() - filemtime($ruta)) > $ttl) {
        return null;
    }
    $contenido = file_get_contents($ruta);
    if ($contenido === false) {
        return null;
    }
    $data = @unserialize($contenido);
    if (!is_array($data)) {
        return null;
    }
    return $data;
}

function guardarCacheDespachoexDetalladoExportacion($key, $data)
{
    $ruta = sys_get_temp_dir() . '/detallado_exportacion_' . md5($key) . '.cache';
    @file_put_contents($ruta, serialize($data), LOCK_EX);
    return $ruta;
}

function cargarDespachoexDetalladoExportacionData($temporada, $empresa, $ttl = 900, $forzar = false)
{
    $cacheKey = implode('|', [session_id(), (string) $empresa, (string) $temporada]);
    if (!$forzar) {
        $cache = obtenerCacheDespachoexDetalladoExportacion($cacheKey, $ttl);
        if ($cache) {
            return $cache;
        }
    }

    $DESPACHOEX_ADO =  new DESPACHOEX_ADO();
    $EMPRESA_ADO = new EMPRESA_ADO();
    $TEMPORADA_ADO = new TEMPORADA_ADO();
    $nombreEmpresa = null;
    if (!empty($empresa)) {
        $empresaData = $EMPRESA_ADO->verEmpresa($empresa);
        if ($empresaData && isset($empresaData[0]['NOMBRE_EMPRESA'])) {
            $nombreEmpresa = $empresaData[0]['NOMBRE_EMPRESA'];
        }
    }

    $nombreTemporada = null;
    if (!empty($temporada)) {
        $temporadaData = $TEMPORADA_ADO->verTemporada($temporada);
        if ($temporadaData && isset($temporadaData[0]['NOMBRE_TEMPORADA'])) {
            $nombreTemporada = $temporadaData[0]['NOMBRE_TEMPORADA'];
        }
    }

    $rows = $DESPACHOEX_ADO->listarDetalladoExportacionDesdeVista($nombreTemporada, $nombreEmpresa);

    $data = [
        'rows' => $rows
    ];

    guardarCacheDespachoexDetalladoExportacion($cacheKey, $data);
    return $data;
}
