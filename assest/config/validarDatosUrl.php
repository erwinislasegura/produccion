<?php

$ACTUALURL = $_SERVER['PHP_SELF'] ?? '';
$OPURL = $_SERVER['QUERY_STRING'] ?? '';

if ($OPURL == "") {
    $id_dato = "";
    $accion_dato = "";
    $_SESSION["urlO"] = "";
}

if ($OPURL != "") {
    $id = $_GET["id"] ?? '';
    $accion = $_GET["a"] ?? '';

    // Si llega querystring sin parametros requeridos, forzar URL limpia.
    if ($id === '' && $accion === '') {
        echo "<script type='text/javascript'> location.href ='" . htmlspecialchars($ACTUALURL, ENT_QUOTES, 'UTF-8') . "';</script>";
    }
}
