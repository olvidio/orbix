<?php
// para que funcione bien la seguridad
use core\ConfigGlobal;

$_POST = $_GET;

$Qacta = $_POST['acta']; //OJO nOfunciona el fiter_input, porque realmente esá en el _GET
$acta = empty($Qacta) ? '' : urldecode($Qacta);
// get the HTML
ob_start();
include(dirname(__FILE__) . '/acta_imprimir_mpdf.php');
$content = ob_get_clean();

// convert to PDF
require_once(ConfigGlobal::$dir_libs . '/vendor/autoload.php');

// quitar los acentos , ñ etc. del nombre
$acta =web\QuitarAcentos::convert($acta);

//$mpdf = new \Mpdf\Mpdf(['','A4','','',10,10,10,10,6,3]);
$config = ['mode' => 'utf-8',
    'format' => 'A4-P',
    'margin_left' => 5,
    'margin_right' => 10,
    'margin_top' => 10,
    'margin_bottom' => 10,
];
$mpdf = new \Mpdf\Mpdf($config);
$mpdf->SetDisplayMode('fullpage');
$mpdf->list_indent_first_level = 0;    // 1 or 0 - whether to indent the first level of a list
$mpdf->WriteHTML($content);
$mpdf->Output("acta($acta).pdf", 'D');