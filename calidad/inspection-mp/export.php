<?php
include_once '../../assest/config/validarUsuarioFruta.php';
include_once '../../assest/controlador/InspectionMPController.php';

$inspectionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($inspectionId <= 0) {
    http_response_code(400);
    exit('Id invalido');
}

$controller = new InspectionMPController();
$spreadsheet = $controller->exportToExcel($inspectionId);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="inspection-mp-' . $inspectionId . '.xlsx"');
header('Cache-Control: max-age=0');
$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;
