<?php
// get the HTML
$_POST = $_GET;

$id_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$id_tabla = (string)filter_input(INPUT_POST, 'id_tabla');

ob_start();
include(dirname(__FILE__) . '/certificado_imprimir_mpdf.php');
$content = ob_get_clean();

// convert to PDF
require_once(core\ConfigGlobal::$dir_libs . '/vendor/autoload.php');

//echo "$content";
//exit();
// quitar los acentos , ñ etc. del nombre
$nom = web\QuitarAcentos::convert($nom);

$config = [
    'format' => 'A4',
    'margin_left' => 5,
    'margin_right' => 5,
    'margin_top' => 6,
    'margin_bottom' => 8,
    'margin_header' => 0,
    'margin_footer' => 5,
];
$mpdf = new \Mpdf\Mpdf($config);
$mpdf->SetDisplayMode('fullpage');
$mpdf->list_indent_first_level = 0;    // 1 or 0 - whether to indent the first level of a list
$mpdf->setHTMLFooter($footer);
$mpdf->WriteHTML($content);
$mpdf->Output("certificado($nom).pdf", 'D');