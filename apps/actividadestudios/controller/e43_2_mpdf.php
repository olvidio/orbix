<?php
// para que funcione bien la seguridad
$_POST = $_GET;

$id_nom = (integer)  filter_input(INPUT_GET, 'id_nom');
$id_activ = (integer)  filter_input(INPUT_GET, 'id_activ');
$go_to = (string) urldecode(filter_input(INPUT_GET, 'go_to'));


// get the HTML
ob_start();
include(dirname(__FILE__).'/e43_imprimir_mpdf.php');
$content = ob_get_clean();

// convert to PDF
require_once(core\ConfigGlobal::$dir_libs.'/vendor/autoload.php');

$mpdf=new mPDF('','A4','','',10,10,10,10,6,3); 
$mpdf->SetDisplayMode('fullpage');
$mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list
$mpdf->WriteHTML($content);
$mpdf->Output();
?>
