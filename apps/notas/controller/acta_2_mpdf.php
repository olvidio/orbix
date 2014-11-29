<?php
$_POST = $_GET;

$acta = empty($_POST['acta'])? '' : urldecode($_POST['acta']);
// get the HTML
ob_start();
include(dirname(__FILE__).'/acta_imprimir_mpdf.php');
$content = ob_get_clean();

// convert to PDF
require_once(core\ConfigGlobal::$dir_libs.'/mpdf/mpdf.php');

$mpdf=new mPDF('','A4','','',10,10,10,10,6,3); 
$mpdf->SetDisplayMode('fullpage');
$mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list
$mpdf->WriteHTML($content);
$mpdf->Output();
?>
