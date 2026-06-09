<?php
// para que funcione bien la seguridad
use frontend\shared\config\OrbixRuntime;

$_POST = $_GET;

$id_nom = (integer)filter_input(INPUT_GET, 'id_nom');
$id_activ = (integer)filter_input(INPUT_GET, 'id_activ');
$nom = '';

ob_start();
include(dirname(__FILE__) . '/e43_imprimir_mpdf.php');
$contentRaw = ob_get_clean();
$content = is_string($contentRaw) ? $contentRaw : '';

require_once OrbixRuntime::dirLibs() . '/vendor/autoload.php';

$nom = frontend\shared\web\QuitarAcentos::convert($nom);

$config = ['mode' => 'utf-8',
    'format' => 'A4-P',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 10,
    'margin_bottom' => 10,
];
$mpdf = new \Mpdf\Mpdf($config);
$mpdf->SetDisplayMode('fullpage');
$mpdf->list_indent_first_level = 0;
$mpdf->WriteHTML($content);
$mpdf->Output("e43($nom).pdf", 'D');
