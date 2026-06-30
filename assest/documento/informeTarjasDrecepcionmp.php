<?php

//LLAMADA ARCHIVOS NECESARIOS PARA LAS OPERACIONES 
include_once '../../assest/controlador/DRECEPCIONMP_ADO.php';
include_once '../../assest/controlador/RECEPCIONMP_ADO.php';
include_once '../../assest/controlador/FOLIO_ADO.php';
include_once '../../assest/controlador/EMPRESA_ADO.php';
include_once '../../assest/controlador/VESPECIES_ADO.php';
include_once '../../assest/controlador/ERECEPCION_ADO.php';
include_once '../../assest/controlador/PRODUCTOR_ADO.php';


//INCIALIZAR LAS VARIBLES
//INICIALIZAR CONTROLADOR
$DRECEPCIONMP_ADO= new DRECEPCIONMP_ADO();
$RECEPCIONMP_ADO = new RECEPCIONMP_ADO();
$FOLIO_ADO =  new FOLIO_ADO();
$EMPRESA_ADO = new EMPRESA_ADO();
$ERECEPCION_ADO =  new ERECEPCION_ADO();
$VESPECIES_ADO = new VESPECIES_ADO();
$PRODUCTOR_ADO =  new PRODUCTOR_ADO();

function claseAjusteTextoTarja($valor, $prefijo, $limiteMedio, $limitePequeno, $limiteExtra)
{
    $texto = trim((string) $valor);
    $largo = function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : strlen($texto);
    if ($largo > $limiteExtra) {
        return $prefijo.'-xs';
    }
    if ($largo > $limitePequeno) {
        return $prefijo.'-sm';
    }
    if ($largo > $limiteMedio) {
        return $prefijo.'-md';
    }
    return '';
}

//INCIALIZAR VARIBALES A OCUPAR PARA LA FUNCIONALIDAD
$IDOP="";
$NUMERORECEPCION="";
$FECHARECEPCION="";
$NUMEROGUIA="";
$HORARECEPCION="";
$TOTALGUIA="";
$FECHAGUIA="";
$FOLIO="";
$PRODUCTOR="";
$ALIASFOLIO="";

$CODIGOPRODUCTOR="";
$NOMBREPRODUCTOR="";
$EMPRESA="";
$EMPRESAURL="";
$FOLIOBASE="";
$TOTALENVASE="";
$TOTALNETO="";
$TOTALBRUTO="";
$CSGPRODUCTOR="";
$TAMAÑO="";

//INICIALIZAR ARREGLOS
$ARRAYRECEPCION="";
$ARRAYDRECEPCION="";
$ARRAYFOLIO="";
$ARRAYEMPRESA="";
$ARRAYVESPECIES="";
$ARRAYPVESPECIES="";
$ARRAYEEXPORTACION="";
$ARRAYPRODUCTOR="";
$ARRAYPRODUCTOR2="";
$ARRAYTIPO="";

if (isset($_REQUEST['parametro']) ) {
    $IDOP = $_REQUEST['parametro'];
}

$ARRAYDRECEPCION = $DRECEPCIONMP_ADO->verDrecepcion2($IDOP); 
if($ARRAYDRECEPCION){
	$ARRAYRECEPCION = $RECEPCIONMP_ADO->verRecepcion3($ARRAYDRECEPCION[0]["ID_RECEPCION"]);
	if($ARRAYRECEPCION){
	
	
		$NUMERORECEPCION=$ARRAYRECEPCION[0]['NUMERO_RECEPCION'];
		$FECHARECEPCION=$ARRAYRECEPCION[0]['FECHA'];
		$HORARECEPCION=$ARRAYRECEPCION[0]['HORA_RECEPCION'];
		$NUMEROGUIA=$ARRAYRECEPCION[0]['NUMERO_GUIA_RECEPCION'];
		$FECHAGUIA=$ARRAYRECEPCION[0]['GUIA'];
		$TOTALGUIA=$ARRAYRECEPCION[0]['TOTAL_KILOS_GUIA_RECEPCION'];
		
		$NOMBRETIPO = $ARRAYRECEPCION[0]['TRECEPCION'];
		if ($NOMBRETIPO == "1") {
		  $NOMBRETIPO = "Desde Productor";
		}
		if ($NOMBRETIPO == "2") {
		  $NOMBRETIPO = "Planta Externa";
		}
		
		$PRODUCTOR=$ARRAYRECEPCION[0]['ID_PRODUCTOR'];
		$PRODUCTOR = $ARRAYRECEPCION[0]['ID_PRODUCTOR'];
		$ARRAYPRODUCTOR = $PRODUCTOR_ADO->verProductor($PRODUCTOR);
		if ($ARRAYPRODUCTOR) {
		  $NOMBREPRODUCTOR = $ARRAYPRODUCTOR[0]['NOMBRE_PRODUCTOR'];
		  $CSGPRODUCTOR = $ARRAYPRODUCTOR[0]['CSG_PRODUCTOR'];
		  $CODIGOPRODUCTOR = !empty($ARRAYPRODUCTOR[0]['CODIGO_ASOCIADO_PRODUCTOR']) ? $ARRAYPRODUCTOR[0]['CODIGO_ASOCIADO_PRODUCTOR'] : $CSGPRODUCTOR;
		}
		
		
		$ARRAYFOLIO=$FOLIO_ADO->verFolio($FOLIO);
		//$ALIASFOLIO=$ARRAYDRECEPCION[0]['ALIAS_FOLIO_DRECEPCION'];
		$ARRAYEMPRESA=$EMPRESA_ADO->verEmpresa($ARRAYRECEPCION[0]['ID_EMPRESA']);
		$EMPRESA=$ARRAYEMPRESA[0]['NOMBRE_EMPRESA'];
		$EMPRESAURL=$ARRAYEMPRESA[0]['LOGO_EMPRESA'];
		
		if($EMPRESAURL==""){
			$EMPRESAURL="img/empresa/no_disponible.png";
		}
	}	
}


//OBTENCION DE LA FECHA
date_default_timezone_set('America/Santiago');
//SE LE PASA LA FECHA ACTUAL A UN ARREGLO
$ARRAYFECHADOCUMENTO =getdate();

//SE OBTIENE INFORMACION RELACIONADA CON LA HORA
$HORA="".$ARRAYFECHADOCUMENTO['hours'];
$MINUTO="".$ARRAYFECHADOCUMENTO['minutes'];
$SEGUNDO="".$ARRAYFECHADOCUMENTO['seconds'];
//EN CASO DE VALORES MENOS A 2 LENGHT, SE LE CONCATENA UN 0
if ($MINUTO < 10) {
    $MINUTO = "0".$MINUTO;
}
if ($SEGUNDO < 10) {
    $SEGUNDO = "0".$SEGUNDO;
}

// SE JUNTA LA INFORMAICON DE LA HORA Y SE LE DA UN FORMATO
$HORAFINAL=$HORA."".$MINUTO."".$SEGUNDO;
$HORAFINAL2=$HORA.":".$MINUTO.":".$SEGUNDO;

//SE OBTIENE INFORMACION RELACIONADA CON LA FECHA
$DIA="".$ARRAYFECHADOCUMENTO['mday'];

$MES="".$ARRAYFECHADOCUMENTO['mon'];
$ANO="".$ARRAYFECHADOCUMENTO['year'];
$NOMBREMES="".$ARRAYFECHADOCUMENTO['month'];
$NOMBREDIA="".$ARRAYFECHADOCUMENTO['weekday'];
//EN CASO DE VALORES MENOS A 2 LENGHT, SE LE CONCATENA UN 0
if ($DIA < 10) {
    $DIA = "0".$DIA;
}
//PARA TRAUDCIR EL MES AL ESPAÑOL
$MESESNOMBRES= array(
    "January" => "Enero",
    "February" => "Febrero",
    "March" => "Marzo",
    "April" => "Abril",
    "May" => "Mayo",
    "June" => "Junio",
    "July" => "Julio",
    "August" => "Agosto",
    "September" => "Septiembre",
    "October" => "Octubre",
    "November" => "Noviembre",
    "December" => "Diciembre"
); 
//PARA TRAUDCIR EL DIA AL ESPAÑOL
$DIASNOMBRES= array(
    "Monday" => "Lunes",
    "Tuesday" => "Martes",
    "Wednesday" => "Miércoles",
    "Thursday" => "Jueves",
    "Friday" => "Viernes",
    "Saturday" => "Sábado",
    "Sunday" => "Domingo"
); 

$NOMBREDIA = $DIASNOMBRES[$NOMBREDIA];
$NOMBREMES = $MESESNOMBRES[$NOMBREMES];
// SE JUNTA LA INFORMAICON DE LA FECHA Y SE LE DA UN FORMATO
$FECHANORMAL=$DIA."".$MES."".$ANO;
$FECHANOMBRE=$NOMBREDIA.", ".$DIA." de ".$NOMBREMES." del ".$ANO;



$html='
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Tarja Recepcion Granel</title>

<style type="text/css">
	body {
		border: 0;
		margin: 0;
		padding: 0;
	}
	.tarja-corporativa {
		border: .7px solid #111827;
		color: #111827;
		font-family: Arial, sans-serif;
		height: 191mm;
		margin: 0;
		padding: 2mm;
		width: 91mm;
		overflow: hidden;
		page-break-inside: avoid;
	}
	.tarja-acento {
		background: #111827;
		height: 3mm;
		margin: -2mm -2mm 3mm -2mm;
	}
	.tarja-header {
		border-bottom: .8px solid #d1d5db;
		padding-bottom: 8px;
	}
	.tarja-logo {
		width: 128px;
		height: 39px;
	}
	.tarja-tipo {
		color: #111827;
		font-size: 13px;
		font-weight: bold;
		letter-spacing: .6px;
		text-align: right;
		text-transform: uppercase;
	}
	.tarja-documento {
		border-bottom: .8px solid #111827;
		font-size: 16px;
		font-weight: bold;
		letter-spacing: .9px;
		margin: 9px 0 4px 0;
		padding-bottom: 7px;
		text-align: center;
		text-transform: uppercase;
	}
	.tarja-label {
		color: #6b7280;
		font-size: 9.5px;
		font-weight: bold;
		letter-spacing: .4px;
		text-transform: uppercase;
	}
	.tarja-value {
		color: #111827;
		font-size: 15px;
		font-weight: bold;
		line-height: 1.05;
	}
	.tarja-ajuste-md {
		font-size: 13px;
	}
	.tarja-ajuste-sm {
		font-size: 11px;
	}
	.tarja-ajuste-xs {
		font-size: 8.5px;
	}
	.tarja-destacado-ajuste-md {
		font-size: 24px;
	}
	.tarja-destacado-ajuste-sm {
		font-size: 18px;
	}
	.tarja-destacado-ajuste-xs {
		font-size: 13px;
	}
	.tarja-row td {
		border-bottom: .6px solid #e5e7eb;
		line-height: 1.05;
		padding: 9px 0;
	}
	.tarja-productor {
		border-bottom: .8px solid #d1d5db;
		border-top: .8px solid #d1d5db;
		margin-top: 10px;
		line-height: 1.05;
		padding: 11px 4px;
		text-align: center;
	}
	.tarja-productor-label {
		color: #6b7280;
		font-size: 10px;
		font-weight: bold;
		letter-spacing: .5px;
		text-transform: uppercase;
	}
	.tarja-productor-nombre {
		color: #111827;
		font-weight: bold;
		line-height: 1.05;
		text-transform: uppercase;
	}
	.tarja-resumen-titulo {
		color: #111827;
		font-size: 10px;
		font-weight: bold;
		letter-spacing: .7px;
		margin-top: 12px;
		padding-bottom: 3px;
		text-align: center;
		text-transform: uppercase;
	}
	.tarja-destacado {
		background: #ffffff;
		border: .8px solid #d1d5db;
		line-height: 1.05;
		padding: 11px 5px;
		text-align: center;
	}
	.tarja-destacado .tarja-label {
		display: block;
		margin-bottom: 3px;
	}
	.tarja-neto {
		font-size: 36px;
		letter-spacing: .4px;
	}
	.tarja-folio {
		font-size: 34px;
		letter-spacing: .4px;
	}
	.tarja-variedad {
		font-size: 22px;
	}
	.tarja-envases {
		font-size: 26px;
	}
	.tarja-qr {
		border-top: .8px solid #d1d5db;
		margin-top: 13px;
		padding-top: 13px;
		text-align: center;
	}
	.tarja-footer {
		color: #6b7280;
		font-size: 10px;
		font-weight: bold;
		letter-spacing: .5px;
		padding-top: 5px;
		text-align: center;
		text-transform: uppercase;
	}
</style>

</head>

<body>
    

';


$TOTALTARJAS = is_array($ARRAYDRECEPCION) ? count($ARRAYDRECEPCION) : 0;
$CONTADORTARJA = 0;
foreach ($ARRAYDRECEPCION as $s) :
    $CONTADORTARJA++;
    
    $ARRAYVESPECIES=$VESPECIES_ADO->verVespecies($s['ID_VESPECIES']);
    $ARRAYEEXPORTACION=$ERECEPCION_ADO->verEstandar($s['ID_ESTANDAR']);
    $NOMBREVARIEDAD = $ARRAYVESPECIES[0]['NOMBRE_VESPECIES'];
    $NOMBREPRODUCTORTARJA = trim((string) $NOMBREPRODUCTOR);
    $NOMBREVARIEDADTARJA = trim((string) $NOMBREVARIEDAD);
    $ESTANDARTARJA = trim((string) $ARRAYEEXPORTACION[0]['NOMBRE_ESTANDAR']);
    $EMPRESATARJA = trim((string) $EMPRESA);
    $NUMERORECEPCIONTARJA = trim((string) $NUMERORECEPCION);
    $NUMEROGUIATARJA = trim((string) $NUMEROGUIA);
    $CSGPRODUCTORTARJA = trim((string) $CSGPRODUCTOR);
    $CODIGOPRODUCTORTARJA = trim((string) $CODIGOPRODUCTOR);
    $FECHARECEPCIONTARJA = trim((string) $FECHARECEPCION);
    $FECHACOSECHATARJA = trim((string) $s['COSECHA']);
    $BRUTOTARJA = trim((string) $s['BRUTO']);
    $NETOTARJA = trim((string) $s['NETO']);
    $FOLIOTARJA = trim((string) $s['FOLIO_DRECEPCION']);
    $ENVASETARJA = trim((string) $s['ENVASE']);
    $CLASERECEPCIONTARJA = claseAjusteTextoTarja($NUMERORECEPCIONTARJA, 'tarja-ajuste', 16, 24, 34);
    $CLASEGUIATARJA = claseAjusteTextoTarja($NUMEROGUIATARJA, 'tarja-ajuste', 16, 24, 34);
    $CLASECSGTARJA = claseAjusteTextoTarja($CSGPRODUCTORTARJA, 'tarja-ajuste', 14, 22, 32);
    $CLASEESTANDARTARJA = claseAjusteTextoTarja($ESTANDARTARJA, 'tarja-ajuste', 18, 28, 42);
    $CLASECODIGOPRODUCTORTARJA = claseAjusteTextoTarja($CODIGOPRODUCTORTARJA, 'tarja-ajuste', 14, 22, 32);
    $CLASENETOTARJA = claseAjusteTextoTarja($NETOTARJA, 'tarja-destacado-ajuste', 8, 12, 18);
    $CLASEFOLIOTARJA = claseAjusteTextoTarja($FOLIOTARJA, 'tarja-destacado-ajuste', 8, 12, 18);
    $CLASEVARIEDADTARJA = claseAjusteTextoTarja($NOMBREVARIEDADTARJA, 'tarja-destacado-ajuste', 14, 22, 32);
    $CLASEENVASETARJA = claseAjusteTextoTarja($ENVASETARJA, 'tarja-destacado-ajuste', 8, 12, 18);
    $DATOSQR = "Codigo productor: ".$CODIGOPRODUCTOR."\n".
        "Numero recepcion: ".$NUMERORECEPCION."\n".
        "Numero guia: ".$NUMEROGUIA."\n".
        "Variedad: ".$NOMBREVARIEDAD."\n".
        "Peso Neto: ".$s['NETO']."\n".
        "Envases: ".$s['ENVASE'];
    $DATOSQRENCODED = htmlspecialchars($DATOSQR, ENT_QUOTES, 'UTF-8');


    if(strlen($NOMBREPRODUCTORTARJA)<="19"){
        $TAMAÑO="f30";
    }
    if(strlen($NOMBREPRODUCTORTARJA)>"19" && strlen($NOMBREPRODUCTORTARJA)<="25"){
        $TAMAÑO="f25";
    }    
    if(strlen($NOMBREPRODUCTORTARJA)>"25" && strlen($NOMBREPRODUCTORTARJA)<="42"){
        $TAMAÑO="f20";
    }
    if(strlen($NOMBREPRODUCTORTARJA)>"42" && strlen($NOMBREPRODUCTORTARJA)<="61"){
        $TAMAÑO="f15";
    }
	if(strlen($NOMBREPRODUCTORTARJA)>"61" && strlen($NOMBREPRODUCTORTARJA)<="70"){
        $TAMAÑO="f13";
    }
    if(strlen($NOMBREPRODUCTORTARJA)>"70"){
        $TAMAÑO="f10";
    }

    $html=$html.'
    <div class="tarja-corporativa">
        <div class="tarja-acento"></div>
        <table class="tarja-header">
            <tr>
                <td style="width:50%; text-align:left; background:transparent; border:0;">
                    <img class="tarja-logo" src="../../assest//img/logo.png" />
                </td>
                <td class="tarja-tipo" style="width:50%; background:transparent; border:0;">'.$NOMBRETIPO.'</td>
            </tr>
        </table>

        <div class="tarja-documento">Tarja recepción materia prima</div>

        <table style="margin-top:9px;">
            <tr class="tarja-row">
                <td style="width:50%; text-align:left; background:transparent;"><span class="tarja-label">Recepción</span><br><span class="tarja-value '.$CLASERECEPCIONTARJA.'">'.$NUMERORECEPCIONTARJA.'</span></td>
                <td style="width:50%; text-align:left; background:transparent;"><span class="tarja-label">Guía</span><br><span class="tarja-value '.$CLASEGUIATARJA.'">'.$NUMEROGUIATARJA.'</span></td>
            </tr>
            <tr class="tarja-row">
                <td style="width:50%; text-align:left; background:transparent;"><span class="tarja-label">Fecha recepción</span><br><span class="tarja-value">'.$FECHARECEPCIONTARJA.'</span></td>
                <td style="width:50%; text-align:left; background:transparent;"><span class="tarja-label">CSG</span><br><span class="tarja-value '.$CLASECSGTARJA.'">'.$CSGPRODUCTORTARJA.'</span></td>
            </tr>
        </table>

        <div class="tarja-productor">
            <div class="tarja-productor-label">Productor</div>
            <div class="tarja-productor-nombre '.$TAMAÑO.'">'.$NOMBREPRODUCTORTARJA.'</div>
        </div>

        <table style="margin-top:9px;">
            <tr class="tarja-row">
                <td style="width:50%; text-align:left; background:transparent;"><span class="tarja-label">Fecha cosecha</span><br><span class="tarja-value">'.$FECHACOSECHATARJA.'</span></td>
                <td style="width:50%; text-align:left; background:transparent;"><span class="tarja-label">Estándar</span><br><span class="tarja-value '.$CLASEESTANDARTARJA.'">'.$ESTANDARTARJA.'</span></td>
            </tr>
            <tr class="tarja-row">
                <td style="width:50%; text-align:left; background:transparent;"><span class="tarja-label">Kilos brutos</span><br><span class="tarja-value">'.$BRUTOTARJA.'</span></td>
                <td style="width:50%; text-align:left; background:transparent;"><span class="tarja-label">Código productor</span><br><span class="tarja-value '.$CLASECODIGOPRODUCTORTARJA.'">'.$CODIGOPRODUCTORTARJA.'</span></td>
            </tr>
        </table>

        <div class="tarja-resumen-titulo">Resumen del folio</div>

        <table style="margin-top:3px;">
            <tr>
                <td class="tarja-destacado" style="width:50%;"><span class="tarja-label">Peso neto</span><br><span class="tarja-value tarja-neto '.$CLASENETOTARJA.'">'.$NETOTARJA.'</span></td>
                <td class="tarja-destacado" style="width:50%;"><span class="tarja-label">N° folio</span><br><span class="tarja-value tarja-folio '.$CLASEFOLIOTARJA.'">'.$FOLIOTARJA.'</span></td>
            </tr>
            <tr>
                <td class="tarja-destacado" style="width:50%;"><span class="tarja-label">Variedad</span><br><span class="tarja-value tarja-variedad '.$CLASEVARIEDADTARJA.'">'.$NOMBREVARIEDADTARJA.'</span></td>
                <td class="tarja-destacado" style="width:50%;"><span class="tarja-label">N° envases</span><br><span class="tarja-value tarja-envases '.$CLASEENVASETARJA.'">'.$ENVASETARJA.'</span></td>
            </tr>
        </table>

        <div class="tarja-qr">
            <barcode code="'.$DATOSQRENCODED.'" size="1.45" type="QR" class="barcode" disableborder="1" />
        </div>
        <div class="tarja-footer">'.$EMPRESATARJA.'</div>
    </div>
    '.($CONTADORTARJA < $TOTALTARJAS ? '<div class="salto" style="page-break-after: always; border: none; margin: 0; padding: 0;"></div>' : '').'
    ';



endforeach; 
$html=$html.'
	
</body>
</html>


';


$html=$html.'
';



//CREACION NOMBRE DEL ARCHIVO
$NOMBREARCHIVO="TarjaRecepionGranel_";
$FECHADOCUMENTO = $FECHANORMAL."_".$HORAFINAL;
$TIPODOCUMENTO="INFORME";
$FORMATO=".pdf";
$NOMBREARCHIVOFINAL=$NOMBREARCHIVO.$FECHADOCUMENTO.$FORMATO;

//CONFIGURACIOND DEL DOCUMENTO
$TIPOPAPEL="";
$ORIENTACION="";

//DETALLE DEL CREADOR DEL INFORME
$TIPOINFORME = "TARJA RECEPCION GRANEL";
$CREADOR = "USUARIO";
$AUTOR = "USUARIO";
$ASUNTO = "TARJA ";


//API DE GENERACION DE PDF
require_once '../../api/mpdf/mpdf/autoload.php';
require_once '../../api/mpdf/qrcode/autoload.php';

$PDF = new \Mpdf\Mpdf([
    'format'=>[100,200],
    'margin_left'=>2,
    'margin_right'=>2,
    'margin_top'=>2,
    'margin_bottom'=>2,
    'margin_header'=>0,
    'margin_footer'=>0
]);
//$PDF = new \Mpdf\Mpdf();
//$PDF = new \Mpdf\Mpdf(['format'=> 'Letter']);

//$mpdf=new mPDF('utf-8','A4');
//$mpdf=new mPDF('utf-8','A4');
//$mpdf=new mPDF('utf-8','A4-L');
//$mpdf=new mPDF('utf-8','A3');
//$mpdf=new mPDF('utf-8','Letter');
//$mpdf=new mPDF('utf-8','150mm 150mm');
//$mpdf=new mPDF('utf-8','11.69in 8.27in');
/*
$PDF->SetHTMLHeader('
    <table width="100%" >
        <tbody>
            <tr>
            </tr>
        </tbody>
    </table>
    <br>
    
');

$PDF->SetHTMLFooter('


    <table width="100%" >
        <tbody>
            <tr>
            </tr>
        </tbody>
    </table>
    
');
*/
$PDF->SetTitle($TIPOINFORME); //titulo pdf
$PDF->SetCreator($CREADOR); //CREADOR PDF
$PDF->SetAuthor($AUTOR); //AUTOR PDF
$PDF->SetSubject($ASUNTO); //ASUNTO PDF

//CONFIGURACION

//$PDF->simpleTables = true; 
//$PDF->packTableData = true;


$stylesheet1 = file_get_contents('../../assest/css/styleTarja.css'); // carga archivo css
$stylesheet2 = file_get_contents('../../assest//css/reset.css'); // carga archivo css
$PDF->WriteHTML($stylesheet1, 1); 
$PDF->WriteHTML($stylesheet2, 1); 
$PDF->WriteHTML($html);
//$PDF->Output();
$PDF->Output($NOMBREARCHIVOFINAL, \Mpdf\Output\Destination::INLINE);

?>