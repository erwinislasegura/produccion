<?php

include_once "../../assest/config/validarUsuarioExpo.php";
include_once '../../assest/modelo/PTUSUARIO.php';

$PTUSUARIO = new PTUSUARIO();

$CONTADOR = 0;
$TUSUARIO = "";
$OP = "";
$SINO = "";

$permissionFields = [
    'FRUTA','FRUTATODO','FAVISO','FRABIERTO','FGRANEL','FGRECEPCION','FGDESPACHO','FGGUIA','FPACKING','FPPROCESO','FPREEMBALEJE','FSAG','FSAGINSPECCION','FFRIGORIFICO','FFRECEPCION','FFRDESPACHO','FFRGUIA','FFRREPALETIZAJE','FFRPC','FFRCFOLIO','FCFRUTA','FCFRECHAZO','FCFLEVANTAMIENTO','FEXISTENCIA',
    'MATERIALES','MRABIERTO','MATERIALESTODO','MMATERIALES','MMATERIALESTODO','MMRECEPION','MMDEAPCHO','MMGUIA','MENVASE','MENVASETODO','MERECEPCION','MEDESPACHO','MEGUIA','MADMINISTRACION','MADMINISTRACIONTODO','MAOC','MAOCAR','MKARDEX','MKARDEXTODO','MKMATERIAL','MKENVASE',
    'EXPORTADORA','EXPORTADORATODO','EMATERIALES','EEXPORTACION','ELIQUIDACION','EPAGO','EFRUTA','EFCICARGA','EINFORMES',
    'ESTADISTICA','ESTADISTICATODO','ESTARVSP','ESTASTOPMP','ESTAINFORME','ESTAEXISTENCIA','ESTAPRODUCTOR',
    'MANTENEDORES','MANTENEDORESTODO','MREGISTRO','MEDITAR','MVER','MELIMINAR','MAGRUPADO',
    'ADMINISTRADOR','ADMINISTRADORTODO','ADUSUARIO','ADAPERTURA','ADAVISO'
];

$persistedPermissionFields = [
    'FRUTA','FAVISO','FRABIERTO','FGRANEL','FGRECEPCION','FGDESPACHO','FGGUIA','FPACKING','FPPROCESO','FPREEMBALEJE','FSAG','FSAGINSPECCION','FFRIGORIFICO','FFRECEPCION','FFRDESPACHO','FFRGUIA','FFRREPALETIZAJE','FFRPC','FFRCFOLIO','FCFRUTA','FCFRECHAZO','FCFLEVANTAMIENTO','FEXISTENCIA',
    'MATERIALES','MRABIERTO','MMATERIALES','MMRECEPION','MMDEAPCHO','MMGUIA','MENVASE','MERECEPCION','MEDESPACHO','MEGUIA','MADMINISTRACION','MAOC','MAOCAR','MKARDEX','MKMATERIAL','MKENVASE',
    'EXPORTADORA','EMATERIALES','EEXPORTACION','ELIQUIDACION','EPAGO','EFRUTA','EFCICARGA','EINFORMES',
    'ESTADISTICA','ESTARVSP','ESTASTOPMP','ESTAINFORME','ESTAEXISTENCIA','ESTAPRODUCTOR',
    'MANTENEDORES','MREGISTRO','MEDITAR','MVER','MELIMINAR','MAGRUPADO',
    'ADMINISTRADOR','ADUSUARIO','ADAPERTURA','ADAVISO'
];

foreach ($permissionFields as $field) {
    $$field = "";
}

$DISABLED = "";
$DISABLED2 = "disabled";
$DISABLEDFRUTA = "disabled";
$DISABLEDFRUTAGRANEL = "disabled";
$DISABLEDFRUTAPACKING = "disabled";
$DISABLEDFRUTASAG = "disabled";
$DISABLEDFRUTAFRIGORIFICO = "disabled";
$DISABLEDFRUTACALIDAD = "disabled";
$DISABLEDMATERIAL = "disabled";
$DISABLEDMMATERIAL = "disabled";
$DISABLEDMENVASE = "disabled";
$DISABLEDMADMINISTRACION = "disabled";
$DISABLEDMMKARDEX = "disabled";
$DISABLEDEXPORTADORA = "disabled";
$DISABLEDMANTENEDORES = "disabled";
$DISABLEDADMINISTRADOR = "disabled";
$DISABLEDESTADISTICA = "disabled";
$DISABLEDESTADISTICAPRODUCTOR = "";

$ARRAYTUSUARIOS = $TUSUARIO_ADO->listarTusuarioCBX();
$ARRAYPTUSUARIO = $PTUSUARIO_ADO->listarPtusuarioCBX();

include_once "../../assest/config/validarDatosUrl.php";
include_once "../../assest/config/datosUrl.php";

$id_dato = isset($_GET['id']) ? $_GET['id'] : "";
$accion_dato = isset($_GET['a']) ? $_GET['a'] : "";

if ($id_dato !== "" && $accion_dato !== "") {
    $IDOP = $id_dato;
    $OP = $accion_dato;

    $accionesValidas = ['0', '1', 'editar', 'ver'];
    if (in_array($OP, $accionesValidas, true)) {
        $ARRAYPTUSUARIOID = $PTUSUARIO_ADO->verPtusuario($IDOP);
        $registro = !empty($ARRAYPTUSUARIOID) ? $ARRAYPTUSUARIOID[0] : null;

        if (!empty($registro)) {
            foreach ($permissionFields as $field) {
                $$field = isset($registro[$field]) ? (string) $registro[$field] : "";
            }
            $FRUTATODO = ($FGRANEL==='1' && $FAVISO==='1' && $FRABIERTO==='1' && $FGRECEPCION==='1' && $FGDESPACHO==='1' && $FGGUIA==='1' && $FPACKING==='1' && $FPPROCESO==='1' && $FPREEMBALEJE==='1' && $FSAG==='1' && $FSAGINSPECCION==='1' && $FFRIGORIFICO==='1' && $FFRECEPCION==='1' && $FFRDESPACHO==='1' && $FFRGUIA==='1' && $FFRREPALETIZAJE==='1' && $FFRPC==='1' && $FFRCFOLIO==='1' && $FCFRUTA==='1' && $FCFRECHAZO==='1' && $FCFLEVANTAMIENTO==='1' && $FEXISTENCIA==='1') ? 'checked' : '';
            $MATERIALESTODO = ($MRABIERTO==='1' && $MMATERIALES==='1' && $MMRECEPION==='1' && $MMDEAPCHO==='1' && $MMGUIA==='1' && $MENVASE==='1' && $MERECEPCION==='1' && $MEDESPACHO==='1' && $MEGUIA==='1' && $MADMINISTRACION==='1' && $MAOC==='1' && $MAOCAR==='1' && $MKARDEX==='1' && $MKMATERIAL==='1' && $MKENVASE==='1') ? 'checked' : '';
            $MMATERIALESTODO = ($MMRECEPION==='1' && $MMDEAPCHO==='1' && $MMGUIA==='1') ? 'checked' : '';
            $MENVASETODO = ($MERECEPCION==='1' && $MEDESPACHO==='1' && $MEGUIA==='1') ? 'checked' : '';
            $MADMINISTRACIONTODO = ($MAOC==='1' && $MAOCAR==='1') ? 'checked' : '';
            $MKARDEXTODO = ($MKMATERIAL==='1' && $MKENVASE==='1') ? 'checked' : '';
            $EXPORTADORATODO = ($EMATERIALES==='1' && $EEXPORTACION==='1' && $ELIQUIDACION==='1' && $EPAGO==='1' && $EFRUTA==='1' && $EFCICARGA==='1' && $EINFORMES==='1') ? 'checked' : '';
            $ESTADISTICATODO = ($ESTARVSP==='1' && $ESTASTOPMP==='1' && $ESTAINFORME==='1' && $ESTAEXISTENCIA==='1' && $ESTAPRODUCTOR==='1') ? 'checked' : '';
            $MANTENEDORESTODO = ($MREGISTRO==='1' && $MEDITAR==='1' && $MVER==='1' && $MELIMINAR==='1' && $MAGRUPADO==='1') ? 'checked' : '';
            $ADMINISTRADORTODO = ($ADUSUARIO==='1' && $ADAPERTURA==='1' && $ADAVISO==='1') ? 'checked' : '';
            $TUSUARIO = isset($registro['ID_TUSUARIO']) ? (string) $registro['ID_TUSUARIO'] : "";
        }

        if (in_array($OP, ['0', '1', 'ver'], true)) {
            $DISABLED = 'disabled';
        }

        if ($OP === 'editar') {
            if ($FRUTA === '1') $DISABLEDFRUTA = '';
            if ($FGRANEL === '1') $DISABLEDFRUTAGRANEL = '';
            if ($FPACKING === '1') $DISABLEDFRUTAPACKING = '';
            if ($FSAG === '1') $DISABLEDFRUTASAG = '';
            if ($FFRIGORIFICO === '1') $DISABLEDFRUTAFRIGORIFICO = '';
            if ($FCFRUTA === '1') $DISABLEDFRUTACALIDAD = '';
            if ($MATERIALES === '1') $DISABLEDMATERIAL = '';
            if ($MMATERIALES === '1') $DISABLEDMMATERIAL = '';
            if ($MENVASE === '1') $DISABLEDMENVASE = '';
            if ($MADMINISTRACION === '1') $DISABLEDMADMINISTRACION = '';
            if ($MKARDEX === '1') $DISABLEDMMKARDEX = '';
            if ($EXPORTADORA === '1') $DISABLEDEXPORTADORA = '';
            if ($ESTADISTICA === '1') $DISABLEDESTADISTICA = '';
            if ($MANTENEDORES === '1') $DISABLEDMANTENEDORES = '';
            if ($ADMINISTRADOR === '1') $DISABLEDADMINISTRADOR = '';
            if ($ESTAPRODUCTOR === '1') $DISABLEDESTADISTICAPRODUCTOR = 'disabled';
        }
    }
}

function getRequestFlag($name) {
    return isset($_REQUEST[$name]) ? 1 : 0;
}

function setFlags(array &$target, array $fields, $value = 1) {
    foreach ($fields as $field) {
        $target[$field] = (int) $value;
    }
}

function allFlagsEnabled(array $target, array $fields) {
    foreach ($fields as $field) {
        if (empty($target[$field])) {
            return 0;
        }
    }
    return 1;
}

function normalizePermissionInput() {
    $flags = [];
    global $persistedPermissionFields;
    foreach ($persistedPermissionFields as $field) {
        $flags[$field] = getRequestFlag($field);
    }

    if (isset($_REQUEST['FRUTATODO'])) {
        setFlags($flags, ['FGRANEL','FAVISO','FRABIERTO','FGRECEPCION','FGDESPACHO','FGGUIA','FPACKING','FPPROCESO','FPREEMBALEJE','FSAG','FSAGINSPECCION','FFRIGORIFICO','FFRECEPCION','FFRDESPACHO','FFRGUIA','FFRREPALETIZAJE','FFRPC','FFRCFOLIO','FCFRUTA','FCFRECHAZO','FCFLEVANTAMIENTO','FEXISTENCIA']);
    }

    if (isset($_REQUEST['MATERIALESTODO'])) {
        setFlags($flags, ['MRABIERTO','MMATERIALES','MMRECEPION','MMDEAPCHO','MMGUIA','MENVASE','MERECEPCION','MEDESPACHO','MEGUIA','MADMINISTRACION','MAOC','MAOCAR','MKARDEX','MKMATERIAL','MKENVASE']);
    }

    if (isset($_REQUEST['MMATERIALESTODO'])) setFlags($flags, ['MMRECEPION','MMDEAPCHO','MMGUIA']);
    if (isset($_REQUEST['MENVASETODO'])) setFlags($flags, ['MERECEPCION','MEDESPACHO','MEGUIA']);
    if (isset($_REQUEST['MADMINISTRACIONTODO'])) setFlags($flags, ['MAOC','MAOCAR']);
    if (isset($_REQUEST['MKARDEXTODO'])) setFlags($flags, ['MKMATERIAL','MKENVASE']);
    if (isset($_REQUEST['EXPORTADORATODO'])) setFlags($flags, ['EMATERIALES','EEXPORTACION','ELIQUIDACION','EPAGO','EFRUTA','EFCICARGA','EINFORMES']);
    if (isset($_REQUEST['ESTADISTICATODO'])) setFlags($flags, ['ESTARVSP','ESTASTOPMP','ESTAINFORME','ESTAEXISTENCIA','ESTAPRODUCTOR']);
    if (isset($_REQUEST['MANTENEDORESTODO'])) setFlags($flags, ['MREGISTRO','MEDITAR','MVER','MELIMINAR','MAGRUPADO']);
    if (isset($_REQUEST['ADMINISTRADORTODO'])) setFlags($flags, ['ADUSUARIO','ADAPERTURA','ADAVISO']);

    if ($flags['FGRANEL']) setFlags($flags, ['FGRECEPCION','FGDESPACHO','FGGUIA']);
    if ($flags['FPACKING']) setFlags($flags, ['FPPROCESO','FPREEMBALEJE']);
    if ($flags['FSAG']) setFlags($flags, ['FSAGINSPECCION']);
    if ($flags['FFRIGORIFICO']) setFlags($flags, ['FFRECEPCION','FFRDESPACHO','FFRGUIA','FFRREPALETIZAJE','FFRPC','FFRCFOLIO']);
    if ($flags['FCFRUTA']) setFlags($flags, ['FCFRECHAZO','FCFLEVANTAMIENTO']);

    $flags['FRUTA'] = ($flags['FRUTA'] || allFlagsEnabled($flags, ['FAVISO','FRABIERTO','FGRANEL','FPACKING','FSAG','FFRIGORIFICO','FCFRUTA','FEXISTENCIA'])) ? 1 : 0;
    $flags['MATERIALES'] = ($flags['MATERIALES'] || allFlagsEnabled($flags, ['MRABIERTO','MMATERIALES','MENVASE','MADMINISTRACION','MKARDEX'])) ? 1 : 0;
    $flags['EXPORTADORA'] = ($flags['EXPORTADORA'] || allFlagsEnabled($flags, ['EMATERIALES','EEXPORTACION','ELIQUIDACION','EPAGO','EFRUTA','EFCICARGA','EINFORMES'])) ? 1 : 0;
    $flags['ESTADISTICA'] = ($flags['ESTADISTICA'] || allFlagsEnabled($flags, ['ESTARVSP','ESTASTOPMP','ESTAINFORME','ESTAEXISTENCIA','ESTAPRODUCTOR'])) ? 1 : 0;
    $flags['MANTENEDORES'] = ($flags['MANTENEDORES'] || allFlagsEnabled($flags, ['MREGISTRO','MEDITAR','MVER','MELIMINAR','MAGRUPADO'])) ? 1 : 0;
    $flags['ADMINISTRADOR'] = ($flags['ADMINISTRADOR'] || allFlagsEnabled($flags, ['ADUSUARIO','ADAPERTURA','ADAVISO'])) ? 1 : 0;

    return $flags;
}

function applyPermissionsToModel($model, $fields) {
    foreach ($fields as $field => $value) {
        $model->__SET($field, $value);
    }
}

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <title>Privilegio Tipo Usuario</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="">
    <meta name="author" content="">
    <?php include_once "../../assest/config/urlHead.php"; ?>
        
    <style>
        .panel-body,
        .content-wrapper,
        .content {
            background: #eef1f4 !important;
        }

        .box {
            border: 1px solid #d1d5db;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
            border-radius: 6px;
            overflow: hidden;
        }

        .box-header.with-border.bg-primary,
        .box-header.with-border.bg-info {
            background: #1f2937 !important;
            border-bottom: 1px solid #111827;
        }

        .box-title {
            color: #f9fafb !important;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: .2px;
        }

        .permissions-intro {
            background: #ffffff;
            color: #111827;
            border: 1px solid #d1d5db;
            border-left: 4px solid #374151;
            border-radius: 4px;
            padding: 12px 14px;
            margin-bottom: 12px;
        }

        .permissions-intro strong { color: #111827; }

        .quick-actions {
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 14px;
        }

        .quick-actions .btn {
            margin: 4px 6px 4px 0;
            border-radius: 3px;
            font-weight: 600;
        }

        .submenu-badge {
            display: inline-block;
            margin-left: 6px;
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 12px;
            background: #e5e7eb;
            color: #374151;
            font-weight: 700;
        }

        .permissions-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        fieldset,
        fieldset.main-menu-card {
            border: 1px solid #d1d5db !important;
            background-color: #ffffff !important;
            border-radius: 4px;
            padding: 12px;
            margin: 0;
            box-shadow: none;
        }

        fieldset legend,
        .main-menu-card > legend {
            margin: 0;
            padding: 3px 8px;
            border: 1px solid #d1d5db;
            border-radius: 3px;
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            background: #f3f4f6 !important;
            width: auto;
        }

        .menu-help {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .form-group label {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 6px;
        }

        .box-body hr {
            border-color: #e5e7eb;
            margin: 10px 0;
        }

        .box-footer {
            background: #f9fafb;
            border-top: 1px solid #d1d5db;
        }

        .box-footer .btn {
            border-radius: 3px;
            font-weight: 700;
            min-width: 120px;
        }

        .table {
            background: #fff;
        }

        .table thead th {
            background: #f3f4f6;
            color: #111827;
            border-bottom: 1px solid #d1d5db;
            font-weight: 700;
        }

        .table td,
        .table th {
            border-color: #e5e7eb;
        }

        @media (max-width: 991px) {
            .permissions-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script type="text/javascript">    


        function fruta(){              
            FRUTA = document.getElementById('FRUTA').checked;
            if(FRUTA==true){    
                document.getElementById('FRUTATODO').disabled = false;  
                document.getElementById('FGRANEL').disabled = false;   
                document.getElementById('FAVISO').disabled = false;   
                document.getElementById('FRABIERTO').disabled = false;   
                document.getElementById('FPACKING').disabled = false;   
                document.getElementById('FFRIGORIFICO').disabled = false;   
                document.getElementById('FSAG').disabled = false;  
                document.getElementById('FCFRUTA').disabled = false;   
                document.getElementById('FEXISTENCIA').disabled = false;   

            }else{
                document.getElementById('FRUTATODO').disabled = true;  
                document.getElementById('FGRANEL').disabled = true;   
                document.getElementById('FAVISO').disabled = true;   
                document.getElementById('FRABIERTO').disabled = true;   
                document.getElementById('FGRECEPCION').disabled = true;   
                document.getElementById('FGDESPACHO').disabled = true;   
                document.getElementById('FGGUIA').disabled = true;   
                document.getElementById('FPACKING').disabled = true;   
                document.getElementById('FPPROCESO').disabled = true;   
                document.getElementById('FPREEMBALEJE').disabled = true;   
                document.getElementById('FSAG').disabled = true;  
                document.getElementById('FSAGINSPECCION').disabled = true;   
                document.getElementById('FFRIGORIFICO').disabled = true;   
                document.getElementById('FFRECEPCION').disabled = true;  
                document.getElementById('FFRDESPACHO').disabled = true;   
                document.getElementById('FFRGUIA').disabled = true;    
                document.getElementById('FFRREPALETIZAJE').disabled = true;   
                document.getElementById('FFRPC').disabled = true;   
                document.getElementById('FFRCFOLIO').disabled = true;   
                document.getElementById('FCFRUTA').disabled = true;   
                document.getElementById('FCFRECHAZO').disabled = true;   
                document.getElementById('FCFLEVANTAMIENTO').disabled = true;   
                document.getElementById('FEXISTENCIA').disabled = true;   
                
                document.getElementById('FRUTATODO').checked = false;  
                document.getElementById('FGRANEL').checked = false;   
                document.getElementById('FAVISO').checked = false;   
                document.getElementById('FRABIERTO').checked = false;   
                document.getElementById('FGRECEPCION').checked = false;   
                document.getElementById('FGDESPACHO').checked = false;   
                document.getElementById('FGGUIA').checked = false;   
                document.getElementById('FPACKING').checked = false;   
                document.getElementById('FPPROCESO').checked = false;   
                document.getElementById('FPREEMBALEJE').checked = false;   
                document.getElementById('FSAG').checked = false;  
                document.getElementById('FSAGINSPECCION').checked = false;   
                document.getElementById('FFRIGORIFICO').checked = false;   
                document.getElementById('FFRECEPCION').checked = false;  
                document.getElementById('FFRDESPACHO').checked = false;  
                document.getElementById('FFRGUIA').checked = false;    
                document.getElementById('FFRREPALETIZAJE').checked = false;   
                document.getElementById('FFRPC').checked = false;   
                document.getElementById('FFRCFOLIO').checked = false;   
                document.getElementById('FCFRUTA').checked = false;   
                document.getElementById('FCFRECHAZO').checked = false;   
                document.getElementById('FCFLEVANTAMIENTO').checked = false;   
                document.getElementById('FEXISTENCIA').checked = false;   
            }
        }         
        function frutagranel(){              
            FGRANEL = document.getElementById('FGRANEL').checked;
            if(FGRANEL==true){    
                document.getElementById('FGRECEPCION').checked = true;   
                document.getElementById('FGDESPACHO').checked = true;   
                document.getElementById('FGGUIA').checked = true;    

            }else{
                document.getElementById('FGRECEPCION').checked = false;   
                document.getElementById('FGDESPACHO').checked = false;   
                document.getElementById('FGGUIA').checked = false;   
                
                document.getElementById('FGRECEPCION').disabled = true;   
                document.getElementById('FGDESPACHO').disabled = true;   
                document.getElementById('FGGUIA').disabled = true;   
            }
        }         
        function frutapacking(){              
            FPACKING = document.getElementById('FPACKING').checked;
            if(FPACKING==true){      
                document.getElementById('FPPROCESO').checked = true;   
                document.getElementById('FPREEMBALEJE').checked = true;   

            }else{  
                document.getElementById('FPPROCESO').checked = false;   
                document.getElementById('FPREEMBALEJE').checked = false;
                
                document.getElementById('FPPROCESO').disabled = true;   
                document.getElementById('FPREEMBALEJE').disabled = true;       
            }
        }         
        function frutasag(){              
            FSAG = document.getElementById('FSAG').checked;
            if(FSAG==true){      
                document.getElementById('FSAGINSPECCION').checked = true;   
            }else{  
                document.getElementById('FSAGINSPECCION').checked = false;   
                
                document.getElementById('FSAGINSPECCION').disabled = true;     
            }
        }     
        function frutafrigorifico(){              
            FFRIGORIFICO = document.getElementById('FFRIGORIFICO').checked;
            if(FFRIGORIFICO==true){          
                document.getElementById('FFRECEPCION').checked = true;  
                document.getElementById('FFRDESPACHO').checked = true;   
                document.getElementById('FFRGUIA').checked = true;  
                document.getElementById('FFRREPALETIZAJE').checked = true;    
                document.getElementById('FFRPC').checked = true;   
                document.getElementById('FFRCFOLIO').checked = true;   

            }else{    
                document.getElementById('FFRECEPCION').checked = false;  
                document.getElementById('FFRDESPACHO').checked = false;   
                document.getElementById('FFRGUIA').checked = false;   
                document.getElementById('FFRREPALETIZAJE').checked = false; 
                document.getElementById('FFRPC').checked = false;   
                document.getElementById('FFRCFOLIO').checked = false;   
                
                document.getElementById('FFRECEPCION').disabled = true;  
                document.getElementById('FFRDESPACHO').disabled = true;   
                document.getElementById('FFRGUIA').disabled = true;   
                document.getElementById('FFRREPALETIZAJE').disabled = true; 
                document.getElementById('FFRPC').disabled = true;   
                document.getElementById('FFRCFOLIO').disabled = true;                   
            }
        } 
        function frutacalidad(){              
            FCFRUTA = document.getElementById('FCFRUTA').checked;
            if(FCFRUTA==true){          
                document.getElementById('FCFRECHAZO').checked = true;   
                document.getElementById('FCFLEVANTAMIENTO').checked = true;   

            }else{   
                document.getElementById('FCFRECHAZO').checked = false;   
                document.getElementById('FCFLEVANTAMIENTO').checked = false;   
                
                document.getElementById('FCFRECHAZO').disabled = true;   
                document.getElementById('FCFLEVANTAMIENTO').disabled = true;  
            }
        }   
        function material(){            
            MATERIALES = document.getElementById('MATERIALES').checked;
            if(MATERIALES==true){  
                document.getElementById('MATERIALESTODO').disabled = false;   
                document.getElementById('MMATERIALES').disabled = false;
                document.getElementById('MMATERIALESTODO').disabled = false;   
                document.getElementById('MRABIERTO').disabled = false;   
                document.getElementById('MENVASE').disabled = false;
                document.getElementById('MENVASETODO').disabled = false;   
                document.getElementById('MADMINISTRACION').disabled = false;
                document.getElementById('MADMINISTRACIONTODO').disabled = false;   
                document.getElementById('MKARDEX').disabled = false;
                document.getElementById('MKARDEXTODO').disabled = false;   
            }else{    
                document.getElementById('MATERIALESTODO').disabled = true;  
                document.getElementById('MRABIERTO').disabled = true;   
                document.getElementById('MMATERIALES').disabled = true;
                document.getElementById('MMATERIALESTODO').disabled = true;   
                document.getElementById('MMRECEPION').disabled = true;   
                document.getElementById('MMDEAPCHO').disabled = true;   
                document.getElementById('MMGUIA').disabled = true;   
                document.getElementById('MENVASE').disabled = true;
                document.getElementById('MENVASETODO').disabled = true;   
                document.getElementById('MERECEPCION').disabled = true;   
                document.getElementById('MEDESPACHO').disabled = true;   
                document.getElementById('MEGUIA').disabled = true; 
                document.getElementById('MADMINISTRACION').disabled = true;
                document.getElementById('MADMINISTRACIONTODO').disabled = true;   
                document.getElementById('MAOC').disabled = true;   
                document.getElementById('MAOCAR').disabled = true; 
                document.getElementById('MKARDEX').disabled = true;
                document.getElementById('MKARDEXTODO').disabled = true;   
                document.getElementById('MKMATERIAL').disabled = true;   
                document.getElementById('MKENVASE').disabled = true;   
                
                document.getElementById('MATERIALESTODO').checked = false;  
                document.getElementById('MRABIERTO').checked = false;   
                document.getElementById('MATERIALES').checked = false;  
                document.getElementById('MMATERIALES').checked = false;
                document.getElementById('MMATERIALESTODO').checked = false;   
                document.getElementById('MMRECEPION').checked = false;   
                document.getElementById('MMDEAPCHO').checked = false;   
                document.getElementById('MMGUIA').checked = false;   
                document.getElementById('MENVASE').checked = false;
                document.getElementById('MENVASETODO').checked = false;   
                document.getElementById('MERECEPCION').checked = false;   
                document.getElementById('MEDESPACHO').checked = false;   
                document.getElementById('MEGUIA').checked = false; 
                document.getElementById('MADMINISTRACION').checked = false;
                document.getElementById('MADMINISTRACIONTODO').checked = false;   
                document.getElementById('MAOC').checked = false;   
                document.getElementById('MAOCAR').checked = false; 
                document.getElementById('MKARDEX').checked = false;
                document.getElementById('MKARDEXTODO').checked = false;   
                document.getElementById('MKMATERIAL').checked = false;   
                document.getElementById('MKENVASE').checked = false;   
            }
        }          
        function mmaterial(){            
            MMATERIALES = document.getElementById('MMATERIALES').checked;
            if(MMATERIALES==true){    
                document.getElementById('MMRECEPION').disabled = false;   
                document.getElementById('MMDEAPCHO').disabled = false;   
                document.getElementById('MMGUIA').disabled = false;   
            }else{                  
                document.getElementById('MMRECEPION').disabled = true;   
                document.getElementById('MMDEAPCHO').disabled = true;   
                document.getElementById('MMGUIA').disabled = true;         
                
                document.getElementById('MMRECEPION').disabled = true;   
                document.getElementById('MMDEAPCHO').disabled = true;    
                document.getElementById('MMGUIA').disabled = true;          
                
            }
        }          
        function menvase(){            
            MENVASE = document.getElementById('MENVASE').checked;
            if(MENVASE==true){    
                document.getElementById('MERECEPCION').disabled = false;   
                document.getElementById('MEDESPACHO').disabled = false;   
                document.getElementById('MEGUIA').disabled = false;  
            }else{                 
                document.getElementById('MERECEPCION').disabled = true;   
                document.getElementById('MEDESPACHO').disabled = true;   
                document.getElementById('MEGUIA').disabled = true;  
                
                document.getElementById('MERECEPCION').disabled = true;   
                document.getElementById('MEDESPACHO').disabled = true;    
                document.getElementById('MEGUIA').disabled = true;      
                
            }
        }  
        function madministracion(){            
            MADMINISTRACION = document.getElementById('MADMINISTRACION').checked;
            if(MADMINISTRACION==true){   
                document.getElementById('MAOC').disabled = false;   
                document.getElementById('MAOCAR').disabled = false;  
            }else{                
                document.getElementById('MAOC').disabled = true;   
                document.getElementById('MAOCAR').disabled = true;  
                
                document.getElementById('MAOC').disabled = true;   
                document.getElementById('MAOCAR').disabled = true;    
            }
        }  
        function mkardex(){            
            MKARDEX = document.getElementById('MKARDEX').checked;
            if(MKARDEX==true){     
                document.getElementById('MKMATERIAL').disabled = false;   
                document.getElementById('MKENVASE').disabled = false; 
            }else{                
                document.getElementById('MKMATERIAL').disabled = true;   
                document.getElementById('MKENVASE').disabled = true; 
                
                document.getElementById('MKMATERIAL').disabled = true;   
                document.getElementById('MKENVASE').disabled = true;    
            }
        }          
        function exportadora(){              
            EXPORTADORA = document.getElementById('EXPORTADORA').checked;
            if(EXPORTADORA==true){    
                document.getElementById('EXPORTADORATODO').disabled = false;   
                document.getElementById('EMATERIALES').disabled = false;   
                document.getElementById('EEXPORTACION').disabled = false;  
                document.getElementById('ELIQUIDACION').disabled = false;  
                document.getElementById('EPAGO').disabled = false;   
                document.getElementById('EFRUTA').disabled = false;    
                document.getElementById('EFCICARGA').disabled = false;   
                document.getElementById('EINFORMES').disabled = false; 
                
            }else{                  
                document.getElementById('EXPORTADORATODO').disabled = true;   
                document.getElementById('EMATERIALES').disabled = true;   
                document.getElementById('EEXPORTACION').disabled = true;    
                document.getElementById('ELIQUIDACION').disabled = true;    
                document.getElementById('EPAGO').disabled = true;    
                document.getElementById('EFRUTA').disabled = true;   
                document.getElementById('EFCICARGA').disabled = true;   
                document.getElementById('EINFORMES').disabled = true;  

                document.getElementById('EXPORTADORATODO').checked = false; 
                document.getElementById('EXPORTADORA').checked = false;   
                document.getElementById('EMATERIALES').checked = false;   
                document.getElementById('EEXPORTACION').checked = false;  
                document.getElementById('ELIQUIDACION').checked = false;  
                document.getElementById('EPAGO').checked = false;   
                document.getElementById('EFRUTA').checked = false;   
                document.getElementById('EFCICARGA').checked = false;     
                document.getElementById('EINFORMES').checked = false;   
            }
        }         
        function estadistica(){              
            ESTADISTICA = document.getElementById('ESTADISTICA').checked;
            if(ESTADISTICA==true){    
                document.getElementById('ESTADISTICATODO').disabled = false; 
                document.getElementById('ESTARVSP').disabled = false;   
                document.getElementById('ESTASTOPMP').disabled = false;   
                document.getElementById('ESTAINFORME').disabled = false;   
                document.getElementById('ESTAEXISTENCIA').disabled = false;     
                document.getElementById('ESTAPRODUCTOR').disabled = false;  

            }else{                  
                document.getElementById('ESTADISTICATODO').disabled = true; 
                document.getElementById('ESTARVSP').disabled = true;   
                document.getElementById('ESTASTOPMP').disabled = true;   
                document.getElementById('ESTAINFORME').disabled = true;    
                document.getElementById('ESTAEXISTENCIA').disabled = true;     
                document.getElementById('ESTAPRODUCTOR').disabled = true;  

                document.getElementById('ESTADISTICATODO').checked = false;   
                document.getElementById('ESTARVSP').checked = false;   
                document.getElementById('ESTASTOPMP').checked = false;   
                document.getElementById('ESTAINFORME').checked = false;    
                document.getElementById('EINFORMES').checked = false;     
                document.getElementById('ESTAEXISTENCIA').checked = false;     
                document.getElementById('ESTAPRODUCTOR').checked = false;  
            }
        }     
        function mantenedores(){              
            MANTENEDORES = document.getElementById('MANTENEDORES').checked;
            if(MANTENEDORES==true){    
                document.getElementById('MANTENEDORESTODO').disabled = false;   
                document.getElementById('MREGISTRO').disabled = false;   
                document.getElementById('MEDITAR').disabled = false;    
                document.getElementById('MVER').disabled = false;   
                document.getElementById('MELIMINAR').disabled = false;
                document.getElementById('MAGRUPADO').disabled = false; 
            }else{                  
                document.getElementById('MANTENEDORESTODO').disabled = true;   
                document.getElementById('MREGISTRO').disabled = true;   
                document.getElementById('MEDITAR').disabled = true;    
                document.getElementById('MVER').disabled = true;     
                document.getElementById('MELIMINAR').disabled = true;
                document.getElementById('MAGRUPADO').disabled = true; 

                document.getElementById('MANTENEDORESTODO').checked = false;  
                document.getElementById('MANTENEDORES').checked = false;   
                document.getElementById('MREGISTRO').checked = false;   
                document.getElementById('MEDITAR').checked = false;    
                document.getElementById('MVER').checked = false;  
                document.getElementById('MELIMINAR').checked = false;
                document.getElementById('MAGRUPADO').checked = false; 
            }
        }         
        function administrador(){              
            ADMINISTRADOR = document.getElementById('ADMINISTRADOR').checked;
            if(ADMINISTRADOR==true){    
                document.getElementById('ADMINISTRADORTODO').disabled = false;   
                document.getElementById('ADUSUARIO').disabled = false;   
                document.getElementById('ADAPERTURA').disabled = false;   
                document.getElementById('ADAVISO').disabled = false;   
            }else{                  
                document.getElementById('ADMINISTRADORTODO').disabled = true;   
                document.getElementById('ADUSUARIO').disabled = true;
                document.getElementById('ADAPERTURA').disabled = true;
                document.getElementById('ADAVISO').disabled = true;
                 
                document.getElementById('ADMINISTRADORTODO').checked = false;   
                document.getElementById('ADMINISTRADOR').checked = false;   
                document.getElementById('ADUSUARIO').checked = false;    
                document.getElementById('ADAPERTURA').checked = false;   
                document.getElementById('ADAVISO').checked = false;   
            }
        }    
        function frutatodo(){              
            FRUTATODO = document.getElementById('FRUTATODO').checked;
            if(FRUTATODO==true){    
                document.getElementById('FGRANEL').checked = true;   
                document.getElementById('FAVISO').checked = true;   
                document.getElementById('FRABIERTO').checked = true;   
                document.getElementById('FGRECEPCION').checked = true;   
                document.getElementById('FGDESPACHO').checked = true;   
                document.getElementById('FGGUIA').checked = true;   
                document.getElementById('FPACKING').checked = true;   
                document.getElementById('FPPROCESO').checked = true;   
                document.getElementById('FPREEMBALEJE').checked = true;   
                document.getElementById('FSAG').checked = true;  
                document.getElementById('FSAGINSPECCION').checked = true;   
                document.getElementById('FFRIGORIFICO').checked = true;   
                document.getElementById('FFRECEPCION').checked = true;  
                document.getElementById('FFRDESPACHO').checked = true;   
                document.getElementById('FFRGUIA').checked = true;   
                document.getElementById('FFRREPALETIZAJE').checked = true;   
                document.getElementById('FFRPC').checked = true;   
                document.getElementById('FFRCFOLIO').checked = true;   
                document.getElementById('FCFRUTA').checked = true;   
                document.getElementById('FCFRECHAZO').checked = true;   
                document.getElementById('FCFLEVANTAMIENTO').checked = true;   
                document.getElementById('FEXISTENCIA').checked = true;   
                
                document.getElementById('FGRANEL').disabled = false;   
                document.getElementById('FRABIERTO').disabled = false;   
                document.getElementById('FGRECEPCION').disabled = false; 
                document.getElementById('FGRECEPCION').disabled = false;   
                document.getElementById('FGDESPACHO').disabled = false;   
                document.getElementById('FGGUIA').disabled = false;   
                document.getElementById('FPACKING').disabled = false;   
                document.getElementById('FPPROCESO').disabled = false;   
                document.getElementById('FPREEMBALEJE').disabled = false;   
                document.getElementById('FSAG').disabled = false;  
                document.getElementById('FSAGINSPECCION').disabled = false;   
                document.getElementById('FFRIGORIFICO').disabled = false;   
                document.getElementById('FFRECEPCION').disabled = false;  
                document.getElementById('FFRDESPACHO').disabled = false;   
                document.getElementById('FFRGUIA').disabled = false;    
                document.getElementById('FFRREPALETIZAJE').disabled = false;   
                document.getElementById('FFRPC').disabled = false;   
                document.getElementById('FFRCFOLIO').disabled = false;   
                document.getElementById('FCFRUTA').disabled = false;   
                document.getElementById('FCFRECHAZO').disabled = false;   
                document.getElementById('FCFLEVANTAMIENTO').disabled = false;   
                document.getElementById('FEXISTENCIA').disabled = false;   

            }else{

                document.getElementById('FGRANEL').disabled = false;   
                document.getElementById('FAVISO').disabled = false;   
                document.getElementById('FRABIERTO').disabled = false;   
                document.getElementById('FGRECEPCION').disabled = true;   
                document.getElementById('FGDESPACHO').disabled = true;   
                document.getElementById('FGGUIA').disabled = true;   
                document.getElementById('FPACKING').disabled = false;   
                document.getElementById('FPPROCESO').disabled = true;   
                document.getElementById('FPREEMBALEJE').disabled = true;   
                document.getElementById('FSAG').disabled = false;  
                document.getElementById('FSAGINSPECCION').disabled = true;   
                document.getElementById('FFRIGORIFICO').disabled = false;   
                document.getElementById('FFRECEPCION').disabled = true;  
                document.getElementById('FFRDESPACHO').disabled = true;   
                document.getElementById('FFRGUIA').disabled = true;    
                document.getElementById('FFRREPALETIZAJE').disabled = true;   
                document.getElementById('FFRPC').disabled = true;   
                document.getElementById('FFRCFOLIO').disabled = true;   
                document.getElementById('FCFRUTA').disabled = false;   
                document.getElementById('FCFRECHAZO').disabled = true;   
                document.getElementById('FCFLEVANTAMIENTO').disabled = true;   
                document.getElementById('FEXISTENCIA').disabled = false;   

                document.getElementById('FGRANEL').checked = false;   
                document.getElementById('FAVISO').checked = false;   
                document.getElementById('FRABIERTO').checked = false;   
                document.getElementById('FGRECEPCION').checked = false;   
                document.getElementById('FGDESPACHO').checked = false;   
                document.getElementById('FGGUIA').checked = false;   
                document.getElementById('FPACKING').checked = false;   
                document.getElementById('FPPROCESO').checked = false;   
                document.getElementById('FPREEMBALEJE').checked = false;   
                document.getElementById('FSAG').checked = false;  
                document.getElementById('FSAGINSPECCION').checked = false;   
                document.getElementById('FFRIGORIFICO').checked = false;   
                document.getElementById('FFRECEPCION').checked = false;  
                document.getElementById('FFRDESPACHO').checked = false;   
                document.getElementById('FFRGUIA').checked = false;   
                document.getElementById('FFRREPALETIZAJE').checked = false;   
                document.getElementById('FFRPC').checked = false;   
                document.getElementById('FFRCFOLIO').checked = false;   
                document.getElementById('FCFRUTA').checked = false;   
                document.getElementById('FCFRECHAZO').checked = false;   
                document.getElementById('FCFLEVANTAMIENTO').checked = false;   
                document.getElementById('FEXISTENCIA').checked = false;   
            }
        }  
        function mmaterialtodo(){
            MMATERIALESTODO = document.getElementById('MMATERIALESTODO').checked;
            document.getElementById('MMRECEPION').checked = MMATERIALESTODO;
            document.getElementById('MMDEAPCHO').checked = MMATERIALESTODO;
            document.getElementById('MMGUIA').checked = MMATERIALESTODO;
        }

        function menvasetodo(){
            MENVASETODO = document.getElementById('MENVASETODO').checked;
            document.getElementById('MERECEPCION').checked = MENVASETODO;
            document.getElementById('MEDESPACHO').checked = MENVASETODO;
            document.getElementById('MEGUIA').checked = MENVASETODO;
        }

        function madministraciontodo(){
            MADMINISTRACIONTODO = document.getElementById('MADMINISTRACIONTODO').checked;
            document.getElementById('MAOC').checked = MADMINISTRACIONTODO;
            document.getElementById('MAOCAR').checked = MADMINISTRACIONTODO;
        }

        function mkardextodo(){
            MKARDEXTODO = document.getElementById('MKARDEXTODO').checked;
            document.getElementById('MKMATERIAL').checked = MKARDEXTODO;
            document.getElementById('MKENVASE').checked = MKARDEXTODO;
        }

        function materialtodo(){              
            MATERIALESTODO = document.getElementById('MATERIALESTODO').checked;
            if(MATERIALESTODO==true){    
                document.getElementById('MRABIERTO').checked = true;   
                document.getElementById('MMATERIALES').checked = true;   
                document.getElementById('MMRECEPION').checked = true;   
                document.getElementById('MMDEAPCHO').checked = true;   
                document.getElementById('MMGUIA').checked = true;   
                document.getElementById('MENVASE').checked = true;   
                document.getElementById('MERECEPCION').checked = true;   
                document.getElementById('MEDESPACHO').checked = true;   
                document.getElementById('MEGUIA').checked = true;  
                document.getElementById('MADMINISTRACION').checked = true;   
                document.getElementById('MAOC').checked = true;   
                document.getElementById('MAOCAR').checked = true;  
                document.getElementById('MKARDEX').checked = true;   
                document.getElementById('MKMATERIAL').checked = true;   
                document.getElementById('MKENVASE').checked = true;
                document.getElementById('MMATERIALESTODO').checked = true;
                document.getElementById('MENVASETODO').checked = true;
                document.getElementById('MADMINISTRACIONTODO').checked = true;
                document.getElementById('MKARDEXTODO').checked = true;
                
                document.getElementById('MRABIERTO').disabled = false;
                document.getElementById('MMATERIALESTODO').disabled = false;
                document.getElementById('MENVASETODO').disabled = false;
                document.getElementById('MADMINISTRACIONTODO').disabled = false;
                document.getElementById('MKARDEXTODO').disabled = false;

                document.getElementById('MMRECEPION').disabled = false;   
                document.getElementById('MMDEAPCHO').disabled = false;   
                document.getElementById('MMGUIA').disabled = false;   
                document.getElementById('MENVASE').disabled = false;   
                document.getElementById('MERECEPCION').disabled = false;   
                document.getElementById('MEDESPACHO').disabled = false;   
                document.getElementById('MEGUIA').disabled = false; 
                document.getElementById('MADMINISTRACION').disabled = false;   
                document.getElementById('MAOC').disabled = false;   
                document.getElementById('MAOCAR').disabled = false; 
                document.getElementById('MKARDEX').disabled = false;   
                document.getElementById('MKMATERIAL').disabled = false;   
                document.getElementById('MKENVASE').disabled = false;                   

            }else{
                document.getElementById('MRABIERTO').checked = false;   
                document.getElementById('MMATERIALES').checked = false;   
                document.getElementById('MMRECEPION').checked = false;   
                document.getElementById('MMDEAPCHO').checked = false;   
                document.getElementById('MMGUIA').checked = false;   
                document.getElementById('MENVASE').checked = false;   
                document.getElementById('MERECEPCION').checked = false;   
                document.getElementById('MEDESPACHO').checked = false;   
                document.getElementById('MEGUIA').checked = false; 
                document.getElementById('MADMINISTRACION').checked = false;   
                document.getElementById('MAOC').checked = false;   
                document.getElementById('MAOCAR').checked = false; 
                document.getElementById('MKARDEX').checked = false;   
                document.getElementById('MKMATERIAL').checked = false;   
                document.getElementById('MKENVASE').checked = false;
                document.getElementById('MMATERIALESTODO').checked = false;
                document.getElementById('MENVASETODO').checked = false;
                document.getElementById('MADMINISTRACIONTODO').checked = false;
                document.getElementById('MKARDEXTODO').checked = false;
                
                document.getElementById('MRABIERTO').disabled = false;
                document.getElementById('MMATERIALESTODO').disabled = false;
                document.getElementById('MENVASETODO').disabled = false;
                document.getElementById('MADMINISTRACIONTODO').disabled = false;
                document.getElementById('MKARDEXTODO').disabled = false;

                document.getElementById('MMRECEPION').disabled = true;   
                document.getElementById('MMDEAPCHO').disabled = true;   
                document.getElementById('MMGUIA').disabled = true;   
                document.getElementById('MENVASE').disabled = false;   
                document.getElementById('MERECEPCION').disabled = true;   
                document.getElementById('MEDESPACHO').disabled = true;   
                document.getElementById('MEGUIA').disabled = true; 
                document.getElementById('MADMINISTRACION').disabled = false;   
                document.getElementById('MAOC').disabled = true;   
                document.getElementById('MAOCAR').disabled = true; 
                document.getElementById('MKARDEX').disabled = false;   
                document.getElementById('MKMATERIAL').disabled = true;   
                document.getElementById('MKENVASE').disabled = true;   

            }
        }  
        function exportadoratodo(){              
            EXPORTADORATODO = document.getElementById('EXPORTADORATODO').checked;
            if(EXPORTADORATODO==true){    
                document.getElementById('EMATERIALES').checked = true;   
                document.getElementById('EEXPORTACION').checked = true;    
                document.getElementById('ELIQUIDACION').checked = true;    
                document.getElementById('EPAGO').checked = true;  
                document.getElementById('EFRUTA').checked = true;  
                document.getElementById('EFCICARGA').checked = true; 
                document.getElementById('EINFORMES').checked = true;  
            }else{
                document.getElementById('EMATERIALES').checked = false;   
                document.getElementById('EEXPORTACION').checked = false;    
                document.getElementById('ELIQUIDACION').checked = false;   
                document.getElementById('EPAGO').checked = false;   
                document.getElementById('EFRUTA').checked = false;   
                document.getElementById('EFCICARGA').checked = false; 
                document.getElementById('EINFORMES').checked = false;   
            }
        }          
        function estadisticatodo(){              
            ESTADISTICATODO = document.getElementById('ESTADISTICATODO').checked;
            if(ESTADISTICATODO==true){    
                document.getElementById('ESTARVSP').checked = true;   
                document.getElementById('ESTASTOPMP').checked = true;   
                document.getElementById('ESTAINFORME').checked = true;  
                document.getElementById('ESTAEXISTENCIA').checked = true;  
            }else{
                document.getElementById('ESTARVSP').checked = false;   
                document.getElementById('ESTASTOPMP').checked = false;   
                document.getElementById('ESTAINFORME').checked = false;    
                document.getElementById('ESTAEXISTENCIA').checked = false;   
            }
        }          
        function estadisticaproductor(){              
            ESTAPRODUCTOR = document.getElementById('ESTAPRODUCTOR').checked;
            if(ESTAPRODUCTOR==true){    
                document.getElementById('ESTADISTICATODO').checked = false;   
                document.getElementById('ESTARVSP').checked = false;   
                document.getElementById('ESTASTOPMP').checked = false;   
                document.getElementById('ESTAINFORME').checked = false;    
                document.getElementById('ESTAEXISTENCIA').checked = false;   
                
                document.getElementById('ESTADISTICATODO').disabled = true;   
                document.getElementById('ESTARVSP').disabled = true;   
                document.getElementById('ESTASTOPMP').disabled = true;   
                document.getElementById('ESTAINFORME').disabled = true;    
                document.getElementById('ESTAEXISTENCIA').disabled = true;    
            }else{                
                document.getElementById('ESTADISTICATODO').disabled = false;  
                document.getElementById('ESTARVSP').disabled = false;   
                document.getElementById('ESTASTOPMP').disabled = false;   
                document.getElementById('ESTAINFORME').disabled = false;    
                document.getElementById('ESTAEXISTENCIA').disabled = false;     
            }
        }  
        function mantenedorestodo(){              
            MANTENEDORESTODO = document.getElementById('MANTENEDORESTODO').checked;
            if(MANTENEDORESTODO==true){    
                document.getElementById('MREGISTRO').checked = true;   
                document.getElementById('MEDITAR').checked = true;    
                document.getElementById('MVER').checked = true;  
                document.getElementById('MELIMINAR').checked = true;
                document.getElementById('MAGRUPADO').checked = true;  
            }else{
                document.getElementById('MREGISTRO').checked = false;   
                document.getElementById('MEDITAR').checked = false;    
                document.getElementById('MVER').checked = false;  
                document.getElementById('MELIMINAR').checked = false;
                document.getElementById('MAGRUPADO').checked = false;  
            }
        }        
        function administradorstodo(){              
            ADMINISTRADORTODO = document.getElementById('ADMINISTRADORTODO').checked;
            if(ADMINISTRADORTODO==true){     
                document.getElementById('ADUSUARIO').checked = true;   
                document.getElementById('ADAPERTURA').checked = true;   
                document.getElementById('ADAVISO').checked = true;   
            }else{
                document.getElementById('ADUSUARIO').checked = false;   
                document.getElementById('ADAPERTURA').checked = false;   
                document.getElementById('ADAVISO').checked = false;   
            }
        }




        function setAllPermissionCheckboxes(checked) {
            const checkboxes = document.querySelectorAll('#form_reg_dato input[type="checkbox"]');
            checkboxes.forEach((cb) => {
                if (!cb.disabled && !cb.id.endsWith('TODO')) {
                    cb.checked = checked;
                }
            });
        }

        function applyPreset(preset) {
            const activateMain = (id, handler) => {
                const el = document.getElementById(id);
                if (el && !el.disabled) {
                    el.checked = true;
                    if (typeof window[handler] === 'function') {
                        window[handler]();
                    }
                }
            };

            setAllPermissionCheckboxes(false);

            ['fruta', 'materiales', 'exportadora', 'estadistica', 'mantenedores', 'administrador'].forEach((fn)=>{
                if (typeof window[fn] === 'function') {
                    window[fn]();
                }
            });

            if (preset === 'lectura') {
                activateMain('FRUTA', 'fruta');
                activateMain('MATERIALES', 'materiales');
                activateMain('EXPORTADORA', 'exportadora');
                activateMain('ESTADISTICA', 'estadistica');
                activateMain('MANTENEDORES', 'mantenedores');

                ['FAVISO','FRABIERTO','ESTAINFORME','MVER'].forEach((id)=>{
                    const el=document.getElementById(id);
                    if(el && !el.disabled){ el.checked=true; }
                });
            }

            if (preset === 'operador') {
                activateMain('FRUTA', 'fruta');
                activateMain('MATERIALES', 'materiales');
                activateMain('EXPORTADORA', 'exportadora');
                activateMain('ESTADISTICA', 'estadistica');
                activateMain('MANTENEDORES', 'mantenedores');

                ['FGRANEL','FGRECEPCION','FGDESPACHO','FGGUIA','FPACKING','FPPROCESO','FPREEMBALEJE','MMATERIALES','MMRECEPION','MMDEAPCHO','MMGUIA','MENVASE','MERECEPCION','MEDESPACHO','MEGUIA','EMATERIALES','EEXPORTACION','ESTAINFORME','MREGISTRO','MVER'].forEach((id)=>{
                    const el=document.getElementById(id);
                    if(el && !el.disabled){ el.checked=true; }
                });
            }

            if (preset === 'admin') {
                activateMain('FRUTA', 'fruta');
                activateMain('MATERIALES', 'materiales');
                activateMain('EXPORTADORA', 'exportadora');
                activateMain('ESTADISTICA', 'estadistica');
                activateMain('MANTENEDORES', 'mantenedores');
                activateMain('ADMINISTRADOR', 'administrador');
                setAllPermissionCheckboxes(true);
            }
        }



        function updateMantenedoresByAccessLevel(level) {
            const map = {
                MREGISTRO: level === 'editar',
                MEDITAR: level === 'editar',
                MVER: level === 'ver' || level === 'editar',
                MELIMINAR: level === 'editar',
                MAGRUPADO: level === 'ver' || level === 'editar'
            };

            Object.keys(map).forEach((id) => {
                const el = document.getElementById(id);
                if (el && !el.disabled) {
                    el.checked = map[id];
                }
            });
        }

        function syncMantenedoresAccessLevel() {
            const canEdit = ['MREGISTRO', 'MEDITAR', 'MELIMINAR'].some((id) => {
                const el = document.getElementById(id);
                return el && el.checked;
            });
            const canView = (() => {
                const el = document.getElementById('MVER');
                return !!(el && el.checked);
            })();
            const level = canEdit ? 'editar' : (canView ? 'ver' : 'sin');
            const selector = document.querySelector('input[name="MANTENEDORES_NIVEL"][value="' + level + '"]');
            if (selector) {
                selector.checked = true;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            ['MREGISTRO','MEDITAR','MVER','MELIMINAR','MAGRUPADO'].forEach((id)=>{
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', syncMantenedoresAccessLevel);
                }
            });
            syncMantenedoresAccessLevel();
        });

        function validacion() {

            TUSUARIO = document.getElementById("TUSUARIO").selectedIndex;
            document.getElementById('val_tusuario').innerHTML = "";

  
            if (TUSUARIO == null || TUSUARIO == 0) {
                document.form_reg_dato.TUSUARIO.focus();
                document.form_reg_dato.TUSUARIO.style.borderColor = "#FF0000";
                document.getElementById('val_tusuario').innerHTML = "NO HA SELECCIONADO NINGUNA ALTERNATIVA";
                return false;
            }
            document.form_reg_dato.TUSUARIO.style.borderColor = "#4AF575";

        }


        function irPagina(url) {
            location.href = "" + url;
        }


  
    </script>


</head>

<body class="exportadora-form-compact hold-transition light-skin fixed sidebar-mini theme-primary sistemRR" >
    <div class="wrapper">
        <?php include_once "../../assest/config/menuExpo.php"; ?>
        <div class="content-wrapper">
            <div class="container-full">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                    <div class="d-flex align-items-center">
                        <div class="mr-auto">
                            <h3 class="page-title">Privilegio Tipo Usuario</h3>
                            <div class="d-inline-block align-items-center">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                                        <li class="breadcrumb-item" aria-current="page">Usuario</li>
                                        <li class="breadcrumb-item active" aria-current="page"> <a href="#">Privilegio Tipo Usuario </a>
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
                    <div class="row">
                        <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 col-xs-12">
                            <div class="box">
                                <div class="box-header with-border bg-primary">
                                    <h4 class="box-title">Registro Privilegio </h4>                                      
                                </div>
                                <!-- /.box-header -->
                                <form data-form-layout="oneline-1" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.65rem;padding:0.65rem 0;border-bottom:1px solid #3a3a3a;" class="form form-one-line" role="form"  method="post"  name="form_reg_dato" id="form_reg_dato" >
                                    <div class="box-body">
                                    <div class="permissions-intro">
                                        <strong>Nuevo panel de control de privilegios:</strong> organice permisos por <strong>Menú</strong> y <strong>Submenú</strong>, incluyendo acciones por usuario como <strong>Ver, Editar y Eliminar</strong>.
                                    </div>

                                    <div class="quick-actions">
                                        <strong>Asignación rápida:</strong>
                                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="setAllPermissionCheckboxes(true)">Marcar todo</button>
                                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="setAllPermissionCheckboxes(false)">Limpiar todo</button>
                                        <button type="button" class="btn btn-xs btn-outline-info" onclick="applyPreset('lectura')">Preset Lectura</button>
                                        <button type="button" class="btn btn-xs btn-outline-warning" onclick="applyPreset('operador')">Preset Operador</button>
                                        <button type="button" class="btn btn-xs btn-outline-success" onclick="applyPreset('admin')">Preset Admin</button>
                                    </div>
                                        <hr class="my-15">                                        
                                        <div class="row">
                                            <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 col-xs-12">
                                                <div class="form-group">
                                                    <label>Tipo Usuario</label>
                                                    <input type="hidden" class="form-control" placeholder="ID" id="ID" name="ID" value="<?php echo $IDOP; ?>" />
                                                    <select class="form-control select2" id="TUSUARIO" name="TUSUARIO" style="width: 100%;"  value="<?php echo $TUSUARIO; ?>"  <?php echo $DISABLED; ?>>
                                                        <option></option>
                                                        <?php foreach ($ARRAYTUSUARIOS as $r) : ?>
                                                            <?php if ($ARRAYTUSUARIOS) {    ?>
                                                                <option value="<?php echo $r['ID_TUSUARIO']; ?>" <?php if ($TUSUARIO == $r['ID_TUSUARIO']) { echo "selected";  } ?>>
                                                                    <?php echo $r['NOMBRE_TUSUARIO'] ?>
                                                                </option>
                                                            <?php } else { ?>
                                                                <option>No Hay Datos Registrados </option>
                                                            <?php } ?>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label id="val_tusuario" class="validacion"> </label>
                                                </div>
                                            </div>
                                        </div>                                                                           
                                        <div class="permissions-grid">
                                        <fieldset class="main-menu-card">
                                            <legend>Fruta <span class="submenu-badge">Menú principal</span></legend>
                                            <p class="menu-help">Control de acceso a módulos de recepción, proceso, SAG, frigorífico, calidad y existencias.</p> 
                                            <div class="row">
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="FRUTA"  name="FRUTA" class="filled-in chk-col-info"      <?php if ($FRUTA == "1") { echo "checked"; } ?>  onchange="fruta();"  <?php echo $DISABLED;?> >
                                                    <label for="FRUTA">Fruta</label>                                        
                                                </div>
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="FRUTATODO"  name="FRUTATODO" class="filled-in chk-col-danger"      <?php echo $FRUTATODO;?>  onchange="frutatodo();"  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                    <label for="FRUTATODO">Selecionar Todo</label>                                        
                                                </div>
                                            </div>      
                                            <hr>
                                            <div class="row">                                             
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="FAVISO"  name="FAVISO" class="filled-in chk-col-success"   <?php if ($FAVISO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                    <label for="FAVISO">Mostrar Avisos</label>	
                                                </div>                                             
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="FRABIERTO"  name="FRABIERTO" class="filled-in chk-col-success"   <?php if ($FRABIERTO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                    <label for="FRABIERTO">Mostrar Registro Abiertos</label>	
                                                </div>  
                                            </div>           
                                            <hr>                    
                                            <fieldset>     
                                                <legend>Granel <span class="submenu-badge">Submenú</span></legend> 
                                                <div class="row">                                            
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FGRANEL"  name="FGRANEL" class="filled-in chk-col-success"   <?php if ($FGRANEL == "1") { echo "checked"; } ?> onchange="frutagranel();"  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FGRANEL">Granel</label>	
                                                    </div>
                                                </div> 
                                                <div class="row">                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FGRECEPCION"  name="FGRECEPCION" class="filled-in chk-col-success"   <?php if ($FGRECEPCION == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FGRECEPCION">Recepcion</label>	
                                                    </div>                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FGDESPACHO"  name="FGDESPACHO" class="filled-in chk-col-success"   <?php if ($FGDESPACHO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FGDESPACHO">Despacho</label>	
                                                    </div>                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FGGUIA"  name="FGGUIA" class="filled-in chk-col-success"   <?php if ($FGGUIA == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FGGUIA">Guía</label>	
                                                    </div>  
                                                </div>
                                            </fieldset>      
                                            <fieldset>     
                                                <legend>Packing <span class="submenu-badge">Submenú</span></legend> 
                                                <div class="row">                                            
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FPACKING"  name="FPACKING" class="filled-in chk-col-success"   <?php if ($FPACKING == "1") { echo "checked"; } ?> onchange="frutapacking();"  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FPACKING">Packing</label>	
                                                    </div>
                                                </div> 
                                                <div class="row">                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FPPROCESO"  name="FPPROCESO" class="filled-in chk-col-success"   <?php if ($FPPROCESO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FPPROCESO">Proceso</label>	
                                                    </div>                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FPREEMBALEJE"  name="FPREEMBALEJE" class="filled-in chk-col-success"   <?php if ($FPREEMBALEJE == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FPREEMBALEJE">Reembalaje</label>	
                                                    </div>   
                                                </div>
                                            </fieldset>
                                            <fieldset>     
                                                <legend>Operaciones Sag <span class="submenu-badge">Submenú</span></legend> 
                                                <div class="row">                                            
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FSAG"  name="FSAG" class="filled-in chk-col-success"   <?php if ($FSAG == "1") { echo "checked"; } ?> onchange="frutasag();"  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FSAG">Operaciones Sag</label>	
                                                    </div>
                                                </div> 
                                                <hr>
                                                <div class="row">                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FSAGINSPECCION"  name="FSAGINSPECCION" class="filled-in chk-col-success"   <?php if ($FSAGINSPECCION == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FSAGINSPECCION">Inspeccion</label>	
                                                    </div>    
                                                </div>
                                            </fieldset>
                                            <fieldset>     
                                                <legend>Frigorifico <span class="submenu-badge">Submenú</span></legend> 
                                                <div class="row">                                            
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FFRIGORIFICO"  name="FFRIGORIFICO" class="filled-in chk-col-success"   <?php if ($FFRIGORIFICO == "1") { echo "checked"; } ?> onchange="frutafrigorifico();"  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FFRIGORIFICO">Frigorifico</label>	
                                                    </div>
                                                </div> 
                                                <hr>
                                                <div class="row">                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FFRECEPCION"  name="FFRECEPCION" class="filled-in chk-col-success"   <?php if ($FFRECEPCION == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FFRECEPCION">Recepción</label>	
                                                    </div>                                            
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FFRDESPACHO"  name="FFRDESPACHO" class="filled-in chk-col-success"   <?php if ($FFRDESPACHO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FFRDESPACHO">Despacho</label>	
                                                    </div>                                            
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FFRGUIA"  name="FFRGUIA" class="filled-in chk-col-success"   <?php if ($FFRGUIA == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FFRGUIA">Guía</label>	
                                                    </div>                                            
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FFRREPALETIZAJE"  name="FFRREPALETIZAJE" class="filled-in chk-col-success"   <?php if ($FFRREPALETIZAJE == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FFRREPALETIZAJE">Repaletizaje</label>	
                                                    </div>                                           
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FFRPC"  name="FFRPC" class="filled-in chk-col-success"   <?php if ($FFRPC == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FFRPC">Planificador carga</label>	
                                                    </div>                                           
                                                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 col-xs-12">
                                                        <input type="checkbox" id="FFRCFOLIO"  name="FFRCFOLIO" class="filled-in chk-col-success"   <?php if ($FFRCFOLIO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FFRCFOLIO">Gestión de folios MP/PT</label>
                                                        <small class="d-block text-muted">Incluye: <strong>Folio Materia Prima</strong> y <strong>Cambiar Folio P. Terminado</strong>.</small>
                                                    </div>
                                                </div>
                                            </fieldset>
                                            <fieldset>     
                                                <legend>Calidad de Fruta </legend> 
                                                <div class="row">                                            
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FCFRUTA"  name="FCFRUTA" class="filled-in chk-col-success"   <?php if ($FCFRUTA == "1") { echo "checked"; } ?> onchange="frutacalidad();"  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FCFRUTA">Calidad de Fruta</label>	
                                                    </div>
                                                </div> 
                                                <hr>
                                                <div class="row">                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FCFRECHAZO"  name="FCFRECHAZO" class="filled-in chk-col-success"   <?php if ($FCFRECHAZO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FCFRECHAZO">Rechazo</label>	
                                                    </div>                                            
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FCFLEVANTAMIENTO"  name="FCFLEVANTAMIENTO" class="filled-in chk-col-success"   <?php if ($FCFLEVANTAMIENTO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FCFLEVANTAMIENTO">Levantamiento</label>	
                                                    </div>  
                                                </div>
                                            </fieldset>
                                            <fieldset>     
                                                <legend>Existencia</legend> 
                                                <div class="row">                                            
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="FEXISTENCIA"  name="FEXISTENCIA" class="filled-in chk-col-success"   <?php if ($FEXISTENCIA == "1") { echo "checked"; } ?> onchange=""  <?php echo $DISABLED;?> <?php echo $DISABLEDFRUTA;?>>
                                                        <label for="FEXISTENCIA">Existencia</label>	
                                                    </div>
                                                </div> 
                                            </fieldset>
                                        </fieldset> 
                                        <fieldset class="main-menu-card">
                                            <legend>Materiales <span class="submenu-badge">Menú principal</span></legend>
                                            <p class="menu-help">Permisos para materiales, envases, administración y kardex.</p> 
                                            <div class="row">
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="MATERIALES"  name="MATERIALES" class="filled-in chk-col-info"      <?php if ($MATERIALES == "1") { echo "checked"; } ?>  onchange="material();"  <?php echo $DISABLED;?> >
                                                    <label for="MATERIALES">Materiales</label>                                        
                                                </div>
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="MATERIALESTODO"  name="MATERIALESTODO" class="filled-in chk-col-danger"      <?php echo $MATERIALESTODO;?>  onchange="materialtodo();"  <?php echo $DISABLED;?> <?php echo $DISABLEDMATERIAL;?>>
                                                    <label for="MATERIALESTODO">Selecionar Todo</label>                                        
                                                </div>
                                            </div>       
                                            <hr>
                                            <div class="row">                                            
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="MRABIERTO"  name="MRABIERTO" class="filled-in chk-col-success"   <?php if ($MRABIERTO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMATERIAL;?>>
                                                    <label for="MRABIERTO">Mostrar Registros Abiertos</label>	
                                                </div>
                                            </div>            
                                            <hr>                    
                                            <fieldset>     
                                                <legend>Material <span class="submenu-badge">Submenú</span></legend> 
                                                <div class="row">                                            
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MMATERIALES"  name="MMATERIALES" class="filled-in chk-col-success"   <?php if ($MMATERIALES == "1") { echo "checked"; } ?> onchange="mmaterial();"  <?php echo $DISABLED;?> <?php echo $DISABLEDMATERIAL;?>>
                                                        <label for="MMATERIALES">Material</label>
                                                    </div>
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MMATERIALESTODO"  name="MMATERIALESTODO" class="filled-in chk-col-danger" <?php echo $MMATERIALESTODO;?> onchange="mmaterialtodo();" <?php echo $DISABLED;?> <?php echo $DISABLEDMATERIAL;?>>
                                                        <label for="MMATERIALESTODO">Seleccionar submenú</label>	
                                                    </div>
                                                </div> 
                                                <div class="row">                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MMRECEPION"  name="MMRECEPION" class="filled-in chk-col-success"   <?php if ($MMRECEPION == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMMATERIAL;?>>
                                                        <label for="MMRECEPION">Recepcion</label>	
                                                    </div>                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MMDEAPCHO"  name="MMDEAPCHO" class="filled-in chk-col-success"   <?php if ($MMDEAPCHO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMMATERIAL;?>>
                                                        <label for="MMDEAPCHO">Despacho</label>	
                                                    </div>                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MMGUIA"  name="MMGUIA" class="filled-in chk-col-success"   <?php if ($MMGUIA == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMMATERIAL;?>>
                                                        <label for="MMGUIA">Guía</label>	
                                                    </div>  
                                                </div>
                                            </fieldset>
                                            <fieldset>    
                                                <legend>Envases <span class="submenu-badge">Submenú</span></legend> 
                                                <div class="row">                                            
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MENVASE"  name="MENVASE" class="filled-in chk-col-success"   <?php if ($MENVASE == "1") { echo "checked"; } ?> onchange="menvase();" <?php echo $DISABLED;?> <?php echo $DISABLEDMATERIAL;?>>
                                                        <label for="MENVASE">Material</label>
                                                    </div>
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MENVASETODO"  name="MENVASETODO" class="filled-in chk-col-danger" <?php echo $MENVASETODO;?> onchange="menvasetodo();" <?php echo $DISABLED;?> <?php echo $DISABLEDMATERIAL;?>>
                                                        <label for="MENVASETODO">Seleccionar submenú</label>	
                                                    </div>
                                                </div> 
                                                <div class="row">                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MERECEPCION"  name="MERECEPCION" class="filled-in chk-col-success"   <?php if ($MERECEPCION == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMENVASE;?>>
                                                        <label for="MERECEPCION">Recepcion</label>	
                                                    </div>                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MEDESPACHO"  name="MEDESPACHO" class="filled-in chk-col-success"   <?php if ($MEDESPACHO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMENVASE;?>>
                                                        <label for="MEDESPACHO">Despacho</label>	
                                                    </div>                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MEGUIA"  name="MEGUIA" class="filled-in chk-col-success"   <?php if ($MEGUIA == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMENVASE;?>>
                                                        <label for="MEGUIA">Guía</label>	
                                                    </div>  
                                                </div>
                                            </fieldset>                                            
                                            <fieldset>    
                                                <legend>Administración <span class="submenu-badge">Submenú</span></legend> 
                                                <div class="row">                                            
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MADMINISTRACION"  name="MADMINISTRACION" class="filled-in chk-col-success"   <?php if ($MADMINISTRACION == "1") { echo "checked"; } ?> onchange="madministracion();" <?php echo $DISABLED;?> <?php echo $DISABLEDMATERIAL;?>>
                                                        <label for="MADMINISTRACION">Administracion</label>
                                                    </div>
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MADMINISTRACIONTODO"  name="MADMINISTRACIONTODO" class="filled-in chk-col-danger" <?php echo $MADMINISTRACIONTODO;?> onchange="madministraciontodo();" <?php echo $DISABLED;?> <?php echo $DISABLEDMATERIAL;?>>
                                                        <label for="MADMINISTRACIONTODO">Seleccionar submenú</label>	
                                                    </div>
                                                </div> 
                                                <div class="row">                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MAOC"  name="MAOC" class="filled-in chk-col-success"   <?php if ($MAOC == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMADMINISTRACION;?>>
                                                        <label for="MAOC">Orden Compra</label>	
                                                    </div>                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MAOCAR"  name="MAOCAR" class="filled-in chk-col-success"   <?php if ($MAOCAR == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMADMINISTRACION;?>>
                                                        <label for="MAOCAR">Orden compra A/R</label>	
                                                    </div>   
                                                </div>
                                            </fieldset>                                                                              
                                            <fieldset>    
                                                <legend>Kardex <span class="submenu-badge">Submenú</span></legend> 
                                                <div class="row">                                            
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MKARDEX"  name="MKARDEX" class="filled-in chk-col-success"   <?php if ($MKARDEX == "1") { echo "checked"; } ?> onchange="mkardex();" <?php echo $DISABLED;?> <?php echo $DISABLEDMATERIAL;?>>
                                                        <label for="MKARDEX">Kardex</label>
                                                    </div>
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MKARDEXTODO"  name="MKARDEXTODO" class="filled-in chk-col-danger" <?php echo $MKARDEXTODO;?> onchange="mkardextodo();" <?php echo $DISABLED;?> <?php echo $DISABLEDMATERIAL;?>>
                                                        <label for="MKARDEXTODO">Seleccionar submenú</label>	
                                                    </div>
                                                </div> 
                                                <div class="row">                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MKMATERIAL"  name="MKMATERIAL" class="filled-in chk-col-success"   <?php if ($MKMATERIAL == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMMKARDEX;?> >
                                                        <label for="MKMATERIAL">Material</label>	
                                                    </div>                                             
                                                    <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                        <input type="checkbox" id="MKENVASE"  name="MKENVASE" class="filled-in chk-col-success"   <?php if ($MKENVASE == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMMKARDEX;?>>
                                                        <label for="MKENVASE">Envase</label>	
                                                    </div>   
                                                </div>
                                            </fieldset>
                                        </fieldset>       
                                        <fieldset class="main-menu-card">
                                            <legend>Exportadora <span class="submenu-badge">Menú principal</span></legend>
                                            <p class="menu-help">Habilita materiales, exportación, liquidación, pago, fruta e informes.</p> 
                                            <div class="row">
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="EXPORTADORA"  name="EXPORTADORA" class="filled-in chk-col-info"      <?php if ($EXPORTADORA == "1") { echo "checked"; } ?>  onchange="exportadora();"  <?php echo $DISABLED;?> >
                                                    <label for="EXPORTADORA">Exportadora</label>                                        
                                                </div>
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="EXPORTADORATODO"  name="EXPORTADORATODO" class="filled-in chk-col-danger"      <?php echo $EXPORTADORATODO;?>  onchange="exportadoratodo();"  <?php echo $DISABLED;?> <?php echo $DISABLEDEXPORTADORA;?>>
                                                    <label for="EXPORTADORATODO">Selecionar Todo</label>                                        
                                                </div>
                                            </div>                                            <div class="row">                                            
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="EMATERIALES"  name="EMATERIALES" class="filled-in chk-col-success"   <?php if ($EMATERIALES == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDEXPORTADORA;?>>
                                                    <label for="EMATERIALES">Materiales</label>	
                                                </div>
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="EEXPORTACION"  name="EEXPORTACION" class="filled-in chk-col-success"     <?php if ($EEXPORTACION == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDEXPORTADORA;?>>
                                                    <label for="EEXPORTACION">Exportación</label>                                        
                                                </div>
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="ELIQUIDACION"  name="ELIQUIDACION" class="filled-in chk-col-success"     <?php if ($ELIQUIDACION == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDEXPORTADORA;?>>
                                                    <label for="ELIQUIDACION">Liquidación</label>                                        
                                                </div>     
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="EPAGO"  name="EPAGO" class="filled-in chk-col-success"     <?php if ($EPAGO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDEXPORTADORA;?>>
                                                    <label for="EPAGO">Pago</label>                                        
                                                </div>        
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="EINFORMES"  name="EINFORMES" class="filled-in chk-col-success"     <?php if ($EINFORMES == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDEXPORTADORA;?>>
                                                    <label for="EINFORMES">Informes</label>                                        
                                                </div>
                                            </div>          
                                            <div class="row">       
                                                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 col-xs-12">                                                                        
                                                    <fieldset>     
                                                        <legend>Fruta <span class="submenu-badge">Submenú</span></legend> 
                                                        <div class="row">                                            
                                                            <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                                <input type="checkbox" id="EFRUTA"  name="EFRUTA" class="filled-in chk-col-success"   <?php if ($EFRUTA == "1") { echo "checked"; } ?>   <?php echo $DISABLED;?> <?php echo $DISABLEDEXPORTADORA;?>>
                                                                <label for="EFRUTA">Fruta</label>	
                                                            </div>
                                                        </div> 
                                                        <hr>
                                                        <div class="row">                                             
                                                            <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                                <input type="checkbox" id="EFCICARGA"  name="EFCICARGA" class="filled-in chk-col-success"   <?php if ($EFCICARGA == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDEXPORTADORA;?>>
                                                                <label for="EFCICARGA">Cambio Instructivo Carga</label>	
                                                            </div>   
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </fieldset>                                          
                                        <fieldset class="main-menu-card">
                                            <legend>Estadística <span class="submenu-badge">Menú principal</span></legend>
                                            <p class="menu-help">Reportería operacional y análisis de existencias/productor.</p> 
                                            <div class="row">
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="ESTADISTICA"  name="ESTADISTICA" class="filled-in chk-col-info"      <?php if ($ESTADISTICA == "1") { echo "checked"; } ?> onchange="estadistica();"  <?php echo $DISABLED;?> >
                                                    <label for="ESTADISTICA">Estadistica</label>                                        
                                                </div>
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="ESTADISTICATODO"  name="ESTADISTICATODO" class="filled-in chk-col-danger"      <?php echo $ESTADISTICATODO;?>  onchange="estadisticatodo();"  <?php echo $DISABLED;?> <?php echo $DISABLEDESTADISTICA;?> <?php echo $DISABLEDESTADISTICAPRODUCTOR;?> >
                                                    <label for="ESTADISTICATODO">Selecionar Todo</label>                                        
                                                </div>
                                            </div>                  
                                            <hr>                    
                                            <div class="row">                                            
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="ESTARVSP"  name="ESTARVSP" class="filled-in chk-col-success"   <?php if ($ESTARVSP == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDESTADISTICA;?> <?php echo $DISABLEDESTADISTICAPRODUCTOR;?> >
                                                    <label for="ESTARVSP">Recepción vs Proceso</label>	
                                                </div>
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="ESTASTOPMP"  name="ESTASTOPMP" class="filled-in chk-col-success"     <?php if ($ESTASTOPMP == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDESTADISTICA;?> <?php echo $DISABLEDESTADISTICAPRODUCTOR;?>>
                                                    <label for="ESTASTOPMP">Stock MP</label>                                        
                                                </div>
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="ESTAINFORME"  name="ESTAINFORME" class="filled-in chk-col-success"     <?php if ($ESTAINFORME == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDESTADISTICA;?> <?php echo $DISABLEDESTADISTICAPRODUCTOR;?>>
                                                    <label for="ESTAINFORME">Informe</label>                                        
                                                </div>
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="ESTAEXISTENCIA"  name="ESTAEXISTENCIA" class="filled-in chk-col-success"     <?php if ($ESTAEXISTENCIA == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDESTADISTICA;?> <?php echo $DISABLEDESTADISTICAPRODUCTOR;?> >
                                                    <label for="ESTAEXISTENCIA">Existencias</label>                                        
                                                </div>
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="ESTAPRODUCTOR"  name="ESTAPRODUCTOR" class="filled-in chk-col-success"     <?php if ($ESTAPRODUCTOR == "1") { echo "checked"; } ?> onchange="estadisticaproductor();" <?php echo $DISABLED;?> <?php echo $DISABLEDESTADISTICA;?>>
                                                    <label for="ESTAPRODUCTOR">Productor</label>                                        
                                                </div>
                                            </div>
                                        </fieldset>
                                        </div>
                                        <fieldset>
                                            <legend>Mantenedores <span class="submenu-badge">Menú principal</span></legend> 
                                            <div class="row">
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="MANTENEDORES"  name="MANTENEDORES" class="filled-in chk-col-info"      <?php if ($MANTENEDORES == "1") { echo "checked"; } ?> onchange="mantenedores();"  <?php echo $DISABLED;?> >
                                                    <label for="MANTENEDORES">Mantenedores</label>                                        
                                                </div>
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="MANTENEDORESTODO"  name="MANTENEDORESTODO" class="filled-in chk-col-danger"      <?php echo $MANTENEDORESTODO;?>  onchange="mantenedorestodo();"  <?php echo $DISABLED;?> <?php echo $DISABLEDMANTENEDORES;?>>
                                                    <label for="MANTENEDORESTODO">Selecionar Todo</label>                                        
                                                </div>
                                            </div>                  
                                            <hr>                    
                                            <div class="row">
                                                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 col-xs-12">
                                                    <div class="access-level-minimal">
                                                        <strong>Nivel de acceso rápido:</strong>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="MANTENEDORES_NIVEL" value="sin" onchange="updateMantenedoresByAccessLevel('sin')" <?php echo $DISABLED;?> <?php echo $DISABLEDMANTENEDORES;?> > Sin acceso
                                                        </label>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="MANTENEDORES_NIVEL" value="ver" onchange="updateMantenedoresByAccessLevel('ver')" <?php echo $DISABLED;?> <?php echo $DISABLEDMANTENEDORES;?> > Ver solamente
                                                        </label>
                                                        <label class="radio-inline">
                                                            <input type="radio" name="MANTENEDORES_NIVEL" value="editar" onchange="updateMantenedoresByAccessLevel('editar')" <?php echo $DISABLED;?> <?php echo $DISABLEDMANTENEDORES;?> > Ver y editar
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">                                            
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="MREGISTRO"  name="MREGISTRO" class="filled-in chk-col-success"   <?php if ($MREGISTRO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMANTENEDORES;?>>
                                                    <label for="MREGISTRO">Registrar (crear)</label>
                                                </div>
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="MEDITAR"  name="MEDITAR" class="filled-in chk-col-success"     <?php if ($MEDITAR == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMANTENEDORES;?>>
                                                    <label for="MEDITAR">Editar (actualizar)</label>                                        
                                                </div>
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="MVER"  name="MVER" class="filled-in chk-col-success"     <?php if ($MVER == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMANTENEDORES;?>>
                                                    <label for="MVER">Ver</label>                                        
                                                </div>
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="MELIMINAR"  name="MELIMINAR" class="filled-in chk-col-success"     <?php if ($MELIMINAR == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMANTENEDORES;?>>
                                                    <label for="MELIMINAR">Eliminar / Deshabilitar</label>                                        
                                                </div>
                                            </div>
                                            <div class="row mt-10">
                                                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 col-xs-12">
                                                    <fieldset>
                                                        <legend>Submenú adicional <span class="submenu-badge">Privilegio</span></legend>
                                                        <div class="row">
                                                            <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 col-xs-12">
                                                                <input type="checkbox" id="MAGRUPADO"  name="MAGRUPADO" class="filled-in chk-col-success"     <?php if ($MAGRUPADO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDMANTENEDORES;?>>
                                                                <label for="MAGRUPADO">Agrupado Privilegio</label>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </fieldset>                                        
                                        <fieldset>
                                            <legend>Administrador <span class="submenu-badge">Menú principal</span></legend>    
                                            <div class="row">
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="ADMINISTRADOR"  name="ADMINISTRADOR" class="filled-in chk-col-info"      <?php if ($ADMINISTRADOR == "1") { echo "checked"; } ?>  onchange="administrador();" <?php echo $DISABLED;?>  >
                                                    <label for="ADMINISTRADOR">Administrador</label>                                        
                                                </div>
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="ADMINISTRADORTODO"  name="ADMINISTRADORTODO" class="filled-in chk-col-danger"      <?php echo $ADMINISTRADORTODO;?>  onchange="administradorstodo();"  <?php echo $DISABLED;?>  <?php echo $DISABLEDADMINISTRADOR;?>>
                                                    <label for="ADMINISTRADORTODO">Selecionar Todo</label>                                        
                                                </div>
                                            </div>                  
                                            <hr>                    
                                            <div class="row">                                            
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="ADUSUARIO"  name="ADUSUARIO" class="filled-in chk-col-success"   <?php if ($ADUSUARIO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDADMINISTRADOR;?>>
                                                    <label for="ADUSUARIO">Usuario</label>	
                                                </div>                                          
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="ADAPERTURA"  name="ADAPERTURA" class="filled-in chk-col-success"   <?php if ($ADAPERTURA == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDADMINISTRADOR;?>>
                                                    <label for="ADAPERTURA">Apertura Registros</label>	
                                                </div>                                          
                                                <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-6 col-sm-6 col-6 col-xs-6">
                                                    <input type="checkbox" id="ADAVISO"  name="ADAVISO" class="filled-in chk-col-success"   <?php if ($ADAVISO == "1") { echo "checked"; } ?>  <?php echo $DISABLED;?> <?php echo $DISABLEDADMINISTRADOR;?>>
                                                    <label for="ADAVISO">Avisos</label>	
                                                </div>
                                            </div>          
                                        </fieldset>
                                    </div>
                                    <!-- /.box-body -->
                                    <div class="box-footer">
                                        <div class="btn-group   col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 col-xs-12 " role="group" aria-label="Acciones generales">                                    
                                            <button type="button" class="btn  btn-warning " data-toggle="tooltip" title="Cancelar" name="CANCELAR" value="CANCELAR" Onclick="irPagina('registroPtusuario.php');">
                                                <i class="ti-trash"></i>Cancelar
                                            </button>
                                            <?php if ($OP == "editar") { ?>
                                                <button type="submit" class="btn btn-primary" name="EDITAR" value="EDITAR"   data-toggle="tooltip" title="Guardar" Onclick="return validacion()">
                                                    <i class="ti-save-alt"></i> Guardar
                                                </button>
                                            <?php } else if($OP == "0") { ?>
                                                <button type="submit" class="btn btn-danger" name="ELIMINAR" value="ELIMINAR"  data-toggle="tooltip" title="Deshabilitar"  >
                                                    <i class="ti-save-alt"></i> Deshabilitar
                                                </button>
                                            <?php } else if($OP == "1"){ ?>                                                    
                                                <button type="submit" class="btn btn-success" name="HABILITAR" value="HABILITAR"  data-toggle="tooltip" title="Habilitar"  >
                                                    <i class="ti-save-alt"></i> Habilitar
                                                </button>
                                            <?php } else { ?>
                                                <button type="submit" class="btn btn-primary" name="GUARDAR" value="GUARDAR"  data-toggle="tooltip" title="Guardar"  <?php echo $DISABLED; ?> Onclick="return validacion()">
                                                    <i class="ti-save-alt"></i> Guardar
                                                </button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- /.box -->
                        </div>
                        <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 col-xs-12">
                            <div class="box">
                                <div class="box-header with-border bg-info">
                                    <h4 class="box-title">Agrupado Privilegio</h4>
                                </div>
                                <div class="box-body">                              
                                    <table id="listar" class="table-hover " style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Número</th>
                                                <th>Tipo Usuario</th>
                                                <th class="text-center">Operaciónes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ARRAYPTUSUARIO as $r) : ?>
                                                <?php $CONTADOR=$CONTADOR+1;?>
                                                <?php 
                                                    $ARRAYVERTUSUARIO=$TUSUARIO_ADO->verTusuario($r['ID_TUSUARIO']);
                                                    if($ARRAYVERTUSUARIO){
                                                      $NOMBRETUSUARIO=$ARRAYVERTUSUARIO[0]["NOMBRE_TUSUARIO"];
                                                    }else{
                                                        $NOMBRETUSUARIO="Sin Datos";
                                                    }
                                                
                                                ?>
                                                <tr class="center">
                                                    <td>
                                                        <a href="#" class="text-warning hover-warning">
                                                            <?php echo $CONTADOR; ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo $NOMBRETUSUARIO; ?></td>             
                                                    <td class="text-center">
                                                                <form class="form-one-line" data-form-layout="oneline-2" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.65rem;padding:0.65rem 0;border-bottom:1px solid #3a3a3a;" method="post" id="form1">
                                                                    <div class="list-icons d-inline-flex">
                                                                        <div class="list-icons-item dropdown">
                                                                            <button class="btn btn-secondary" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                <span class="icon-copy ti-settings"></span>
                                                                            </button>
                                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                                <input type="hidden" class="form-control" placeholder="ID" id="ID" name="ID" value="<?php echo $r['ID_PTUSUARIO']; ?>" />
                                                                                <input type="hidden" class="form-control" placeholder="URL" id="URL" name="URL" value="registroPtusuario" />
                                                                                <?php if($PMVER=="1" || $PADMINISTRADOR=="1"){ ?>
                                                                                <span href="#" class="dropdown-item" data-toggle="tooltip" title="Ver">
                                                                                    <button type="submit" class="btn btn-info btn-block  btn-sm" id="VERURL" name="VERURL">
                                                                                        <i class="ti-eye"></i> Ver
                                                                                    </button>
                                                                                </span>
                                                                                <?php } ?> 
                                                                                <?php if($PMEDITAR=="1" || $PADMINISTRADOR=="1"){ ?>
                                                                                <span href="#" class="dropdown-item" data-toggle="tooltip" title="Editar">
                                                                                    <button type="submit" class="btn  btn-warning btn-block   btn-sm" id="EDITARURL" name="EDITARURL">
                                                                                        <i class="ti-pencil-alt"></i> Editar
                                                                                    </button>
                                                                                </span>
                                                                                <?php } ?>
                                                                                <?php if (($PMELIMINAR=="1" || $PADMINISTRADOR=="1") && $r['ESTADO_REGISTRO'] == 1) { ?>
                                                                                    <span href="#" class="dropdown-item" data-toggle="tooltip" title="Deshabilitar">
                                                                                        <button type="submit" class="btn btn-block btn-danger btn-sm" id="ELIMINARURL" name="ELIMINARURL">
                                                                                            <i class="ti-na "></i> Deshabilitar
                                                                                        </button>
                                                                                    </span>                                                                                
                                                                                <?php } ?>
                                                                                <?php if (($PMELIMINAR=="1" || $PADMINISTRADOR=="1") && $r['ESTADO_REGISTRO'] == 0) { ?>
                                                                                    <span href="#" class="dropdown-item" data-toggle="tooltip" title="Habilitar">
                                                                                        <button type="submit" class="btn btn-block btn-success btn-sm" id="HABILITARURL" name="HABILITARURL">
                                                                                            <i class="ti-check "></i> Habilitar
                                                                                        </button>
                                                                                    </span>    
                                                                                <?php } ?>                                                       
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </td>                                               
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.box -->
                    </div>
                </section>
            <!-- /.content -->
            </div>
            <!--.row -->

        </div>
    </div>
    <?php include_once "../../assest/config/footer.php"; ?>
    <?php include_once "../../assest/config/menuExtraExpo.php"; ?>
    </div>
    <?php include_once "../../assest/config/urlBase.php"; ?>

    <?php 
                  //OPERACIONES
            //OPERACION DE REGISTRO DE FILA
            if (isset($_REQUEST['GUARDAR'])) {
                $ARRAYPTUSUARIOVALIDAR=$PTUSUARIO_ADO->listarPtusuarioPorTusuarioCBX($_REQUEST['TUSUARIO']);
                if($ARRAYPTUSUARIOVALIDAR){
                    $SINO="1";
                    echo '<script>
                            Swal.fire({
                                icon:"warning",
                                title:"Accion restringida",
                                text:"Existe un registro asociado al tipo usuario selecionado",
                                showConfirmButton: true,
                                confirmButtonText:"Cerrar",
                                closeOnConfirm:false
                            })
                        </script>';
                }else{
                    $SINO="0";
                }
                if($SINO=="0"){
                    //UTILIZACION METODOS SET DEL MODELO
                    //SETEO DE ATRIBUTOS DE LA CLASE, OBTENIDO EN EL FORMULARIO   
                    $permissions = normalizePermissionInput();
                    applyPermissionsToModel($PTUSUARIO, $permissions);
                    $PTUSUARIO->__SET('ID_USUARIOI', $IDUSUARIOS);
                    $PTUSUARIO->__SET('ID_USUARIOM', $IDUSUARIOS);
                    $PTUSUARIO->__SET('ID_TUSUARIO', $_REQUEST['TUSUARIO']);
                    //LLAMADA AL METODO DE REGISTRO DEL CONTROLADOR
                    $PTUSUARIO_ADO->agregarPtusuario($PTUSUARIO);
                   
                    $AUSUARIO_ADO->agregarAusuario2("NULL",3,1,"".$_SESSION["NOMBRE_USUARIO"].", Registro de Privilegio.","usuario_ptusuario","NULL",$_SESSION["ID_USUARIO"],$_SESSION['ID_EMPRESA'],'NULL',$_SESSION['ID_TEMPORADA'] );           
                   

                    //REDIRECCIONAR A PAGINA registroPtusuario.php                    
                    echo '<script>
                        Swal.fire({
                            icon:"success",
                            title:"Registro Creado",
                            text:"El registro de Privilegio se ha creado correctamente",
                            showConfirmButton: true,
                            confirmButtonText:"Cerrar",
                            closeOnConfirm:false
                        }).then((result)=>{
                            location.href = "registroPtusuario.php";                            
                        })
                    </script>';
                }
            }
            //OPERACION EDICION DE FILA
            if (isset($_REQUEST['EDITAR'])) {
                //UTILIZACION METODOS SET DEL MODELO
                //SETEO DE ATRIBUTOS DE LA CLASE, OBTENIDO EN EL FORMULARIO  
                $permissions = normalizePermissionInput();
                applyPermissionsToModel($PTUSUARIO, $permissions);
                $PTUSUARIO->__SET('ID_USUARIOM', $IDUSUARIOS);
                $PTUSUARIO->__SET('ID_PTUSUARIO', $_REQUEST['ID']);
                //LLAMADA AL METODO DE EDICION DEL CONTROLADOR
                $PTUSUARIO_ADO->actualizarPtusuario($PTUSUARIO);
                
                $AUSUARIO_ADO->agregarAusuario2("NULL",3,2,"".$_SESSION["NOMBRE_USUARIO"].", Modificación de Privilegio.","usuario_ptusuario", $_REQUEST['ID'],$_SESSION["ID_USUARIO"],$_SESSION['ID_EMPRESA'],'NULL',$_SESSION['ID_TEMPORADA'] );     

                //REDIRECCIONAR A PAGINA registroPtusuario.php                              
                echo '<script>
                    Swal.fire({
                        icon:"info",
                        title:"Registro Modificado",
                        text:"El registro de Privilegio se ha modificado correctamente",
                        showConfirmButton: true,
                        confirmButtonText:"Cerrar",
                        closeOnConfirm:false
                    }).then((result)=>{
                        location.href = "registroPtusuario.php";                            
                    })
                    </script>';
            }
            
            
            if (isset($_REQUEST['ELIMINAR'])) {

                

                $PTUSUARIO->__SET('ID_PTUSUARIO', $_REQUEST['ID']);
                $PTUSUARIO_ADO->deshabilitar($PTUSUARIO);

                $AUSUARIO_ADO->agregarAusuario2("NULL",3,4,"".$_SESSION["NOMBRE_USUARIO"].", Deshabilitar  Privilegio.","usuario_ptusuario", $_REQUEST['ID'],$_SESSION["ID_USUARIO"],$_SESSION['ID_EMPRESA'],'NULL',$_SESSION['ID_TEMPORADA'] );                
                
                echo '<script>
                    Swal.fire({
                        icon:"error",
                        title:"Registro Modificado",
                        text:"El registro de Privilegio se ha Deshabilitado correctamente", 
                        showConfirmButton: true,
                        confirmButtonText:"Cerrar",
                        closeOnConfirm:false
                    }).then((result)=>{
                        location.href = "registroPtusuario.php";                            
                    })
                </script>';
            }
            
            if (isset($_REQUEST['HABILITAR'])) {

                $PTUSUARIO->__SET('ID_PTUSUARIO', $_REQUEST['ID']);
                $PTUSUARIO_ADO->habilitar($PTUSUARIO);

                $AUSUARIO_ADO->agregarAusuario2("NULL",3,5,"".$_SESSION["NOMBRE_USUARIO"].", Habilitar Privilegio.","usuario_ptusuario", $_REQUEST['ID'],$_SESSION["ID_USUARIO"],$_SESSION['ID_EMPRESA'],'NULL',$_SESSION['ID_TEMPORADA'] );                               

                echo '<script>
                    Swal.fire({
                        icon:"success",
                        title:"Registro Modificado",
                        text:"El registro de Privilegio se ha Habilitado correctamente", 
                        showConfirmButton: true,
                        confirmButtonText:"Cerrar",
                        closeOnConfirm:false
                    }).then((result)=>{
                        location.href = "registroPtusuario.php";                            
                    })
                </script>';
            }
    
    ?>
</body>
</html>
