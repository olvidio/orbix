<?php

use src\shared\config\ConfigGlobal;

// Abierto via `window.open` (GET). El incluido `tessera_imprimir_mpdf.php`
// ya lee `$_GET` directamente, asi que no hace falta reasignar `$_POST`.
$id_nom = (int)filter_input(INPUT_GET, 'id_nom');
$id_tabla = (string)filter_input(INPUT_GET, 'id_tabla');

ob_start();
include __DIR__ . '/tessera_imprimir_mpdf.php';
$content = ob_get_clean();

require_once ConfigGlobal::$dir_libs . '/vendor/autoload.php';

$nom_archivo = isset($nom) ? web\QuitarAcentos::convert($nom) : (string)$id_nom;

$config = [
    'mode' => 'utf-8',
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
$mpdf->Output("tessera($nom_archivo).pdf", 'D');
