<?php
/**
 * Tessera de una persona (vista HTML): muestra por cada asignatura del
 * bienio+cuadrienio si esta pendiente, cursada o aprobada, con nota y fecha.
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        22/11/02.
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (empty($a_sel)) {
    exit('no sé de que va');
}

$oView = new ViewNewPhtml('frontend\\notas\\controller');
foreach ($a_sel as $PersonaSel) {
    $id_nom = (integer)strtok($PersonaSel, "#");
    $payload = PostRequest::getDataFromUrl('/src/notas/tessera_ver_data', [
        'id_nom' => $id_nom,
    ]);
    $a_campos = is_array($payload) ? $payload : [];
    $a_campos['oPosicion'] = $oPosicion;
    $oView->renderizar('tesera_ver.phtml', $a_campos);
}
