<?php

if (!function_exists('redirigirOperacionMantenedor')) {
    function redirigirOperacionMantenedor($accionDato)
    {
        $idDato = isset($_REQUEST['ID']) ? (int)$_REQUEST['ID'] : 0;
        $urlBase = isset($_REQUEST['URL']) ? basename((string)$_REQUEST['URL']) : '';

        if ($idDato <= 0 || $urlBase === '') {
            return;
        }

        $urlDestino = sprintf("%s.php?op&id=%d&a=%s", $urlBase, $idDato, rawurlencode($accionDato));
        echo "<script type='text/javascript'> location.href = " . json_encode($urlDestino) . ";</script>";
    }
}

if (isset($_REQUEST['VERURL'])) {
    redirigirOperacionMantenedor('ver');
}

if (isset($_REQUEST['EDITARURL'])) {
    redirigirOperacionMantenedor('editar');
}

if (isset($_REQUEST['ELIMINARURL'])) {
    redirigirOperacionMantenedor('0');
}

if (isset($_REQUEST['HABILITARURL'])) {
    redirigirOperacionMantenedor('1');
}
