<?php
// get the HTML
$_POST = $_GET;

$id_nom = (integer) \filter_input(INPUT_POST, 'id_nom');
$id_tabla = (string) \filter_input(INPUT_POST, 'id_tabla');

ob_start();
include(dirname(__FILE__).'/tessera_imprimir_mpdf.php');
$content = ob_get_clean();

// convert to PDF
require_once(core\ConfigGlobal::$dir_libs.'/vendor/autoload.php');

// quitar los acentos , ñ etc. del nombre
$nom = web\QuitarAcentos::convert($nom);

$mpdf = new mPDF('','A4','','',10,10,10,10,6,3); 
$mpdf->SetDisplayMode('fullpage');
$mpdf->list_indent_first_level = 0;	// 1 or 0 - whether to indent the first level of a list
$mpdf->WriteHTML($content);
$mpdf->Output("tessera($nom).pdf",'D');