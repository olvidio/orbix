<?php
/**
 * Esta página sirve para la tessera de una persona.
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        22/11/02.
 *
 */

use notas\model\Tesera;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_nom = (integer)strtok($a_sel[0], "#");
    $id_tabla = (string)strtok("#");
} else {
    exit('no sé de que va');
}

$oTesera = new Tesera();
$p = 0;
foreach ($a_sel as $PersonaSel) {
    $p++;
    $id_nom = (integer)strtok($PersonaSel, "#");
    echo $oTesera->verTesera($id_nom);
}