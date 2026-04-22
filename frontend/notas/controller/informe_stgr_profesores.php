<?php

use frontend\shared\model\ViewNewPhtml;
use src\notas\application\InformeStgrProfesores;

/**
 * Informe anual STGR - Profesores (puntos 36..47).
 *
 * Orquestacion delgada: delega el calculo en el use case y renderiza la
 * tabla compartida `informe_stgr_tabla.phtml`.
 *
 * @package    delegacion
 * @subpackage estudios
 */

require_once 'frontend/shared/global_header_front.inc';

$Qlista = (string)filter_input(INPUT_POST, 'lista');
$lista = !empty($Qlista);

$oInforme = new InformeStgrProfesores();
$datos = $oInforme->calcular($lista);

$a_campos = [
    'titulo' => \core\strtoupper_dlb(_("profesores stgr")),
    'curso_txt' => $datos['curso_txt'],
    'res' => $datos['res'],
    'textos' => $datos['textos'],
    'oPosicion' => $oPosicion,
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('informe_stgr_tabla.phtml', $a_campos);
