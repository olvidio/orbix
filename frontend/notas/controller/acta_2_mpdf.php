<?php

use frontend\shared\config\OrbixRuntime;

// Este controlador se abre via `window.open` (GET). El incluido
// `acta_imprimir_mpdf.php` solo usa la variable `$acta` de scope y
// `filter_input(INPUT_POST, ...)` para `refresh`, por lo que leemos el
// parametro directamente de GET sin necesidad de reasignar `$_POST`.
$acta = (string)filter_input(INPUT_GET, 'acta');
$acta = empty($acta) ? '' : urldecode($acta);

ob_start();
include __DIR__ . '/acta_imprimir_mpdf.php';
$content = ob_get_clean();

require_once OrbixRuntime::dirLibs() . '/vendor/autoload.php';

$acta_nombre = frontend\shared\web\QuitarAcentos::convert($acta);

$config = [
    'mode' => 'utf-8',
    'format' => 'A4-P',
    'margin_left' => 5,
    'margin_right' => 10,
    'margin_top' => 10,
    'margin_bottom' => 10,
];
$mpdf = new \Mpdf\Mpdf($config);
$mpdf->SetDisplayMode('fullpage');
$mpdf->list_indent_first_level = 0;
$mpdf->WriteHTML($content);
$mpdf->Output("acta($acta_nombre).pdf", 'D');
