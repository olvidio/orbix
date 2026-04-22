<?php

use frontend\shared\model\ViewNewPhtml;
use src\notas\application\InformeStgrNumerarios;

/**
 * Informe anual STGR - Numerarios (puntos 1..18 + `x`).
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

$oInforme = new InformeStgrNumerarios();
$ce_lugar = $oInforme->resolverCeLugar($Qdl);

if (empty($ce_lugar)) {
    echo _("No está definido el ce para esta dl/r");
    echo '<br>';
    echo _("Hay definirlo en los parámetros de configuración.");
    echo '<br>';
}

$datos = $oInforme->calcular($Qdl, $lista, (string)$ce_lugar);

$a_campos = [
    'titulo' => \core\strtoupper_dlb(_("alumnos numerarios")),
    'curso_txt' => $datos['curso_txt'],
    'res' => $datos['res'],
    'textos' => $datos['textos'],
    'oPosicion' => $oPosicion,
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('informe_stgr_tabla.phtml', $a_campos);
