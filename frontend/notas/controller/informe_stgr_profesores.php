<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\helpers\FuncTablasSupport;

use frontend\shared\FrontBootstrap;

/**
 * Informe anual STGR - Profesores (puntos 36..47).
 *
 * Orquestacion delgada: delega el calculo en `/src/notas/informe_stgr_profesores_data`
 * y renderiza la tabla compartida `informe_stgr_tabla.phtml`.
 *
 * @package    delegacion
 * @subpackage estudios
 */

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$Qlista = (string)filter_input(INPUT_POST, 'lista');

$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    ['lista' => $Qlista],
);

$datos = PostRequest::getDataFromUrl('/src/notas/informe_stgr_profesores_data', [
    'lista' => $Qlista,
]);

$a_campos = [
    'titulo' => \src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb(_("profesores stgr")),
    'curso_txt' => $datos['curso_txt'],
    'res' => $datos['res'],
    'textos' => $datos['textos'],
    'avisos_html' => '',
    'oPosicion' => $oPosicion,
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('informe_stgr_tabla.phtml', $a_campos);
