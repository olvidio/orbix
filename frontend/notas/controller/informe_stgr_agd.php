<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\helpers\FuncTablasSupport;

use frontend\shared\FrontBootstrap;

/**
 * Informe anual STGR - Agregados (puntos 21..33 + `x`).
 *
 * Orquestacion delgada: delega el calculo en `/src/notas/informe_stgr_agd_data`
 * y renderiza la tabla compartida `informe_stgr_tabla.phtml`.
 *
 * @package    delegacion
 * @subpackage estudios
 */

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$QdlRaw = filter_input(INPUT_POST, 'dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qdl = is_array($QdlRaw) ? $QdlRaw : [];
$Qlista = (string)filter_input(INPUT_POST, 'lista');

$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    ['dl' => $Qdl, 'lista' => $Qlista],
);

$datos = PostRequest::getDataFromUrl('/src/notas/informe_stgr_agd_data', [
    'dl' => $Qdl,
    'lista' => $Qlista,
]);

$a_campos = [
    'titulo' => \src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb(_("alumnos agregados")),
    'curso_txt' => $datos['curso_txt'],
    'res' => $datos['res'],
    'textos' => $datos['textos'],
    'oPosicion' => $oPosicion,
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('informe_stgr_tabla.phtml', $a_campos);
