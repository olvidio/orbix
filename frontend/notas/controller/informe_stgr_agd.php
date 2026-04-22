<?php

use frontend\shared\model\ViewNewPhtml;
use src\notas\application\InformeStgrAgregados;

/**
 * Informe anual STGR - Agregados (puntos 21..33 + `x`).
 *
 * Orquestacion delgada: delega el calculo en el use case y renderiza la
 * tabla compartida `informe_stgr_tabla.phtml`.
 *
 * @package    delegacion
 * @subpackage estudios
 */

require_once 'frontend/shared/global_header_front.inc';

$Qdl = (array)filter_input(INPUT_POST, 'dl', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qlista = (string)filter_input(INPUT_POST, 'lista');
$lista = !empty($Qlista);

$oInforme = new InformeStgrAgregados();
$datos = $oInforme->calcular($Qdl, $lista);

$a_campos = [
    'titulo' => \core\strtoupper_dlb(_("alumnos agregados")),
    'curso_txt' => $datos['curso_txt'],
    'res' => $datos['res'],
    'textos' => $datos['textos'],
    'oPosicion' => $oPosicion,
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('informe_stgr_tabla.phtml', $a_campos);
