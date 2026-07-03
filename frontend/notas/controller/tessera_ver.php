<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\ListNavSupport;

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
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
ListNavSupport::bootRecordar($oPosicion);
ListNavSupport::persistTesseraReturnToPosicion($oPosicion, 0);
ListNavSupport::persistSelectionToPosicion($oPosicion, 1);

$a_sel_raw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$a_sel = is_array($a_sel_raw) ? $a_sel_raw : [];
if ($a_sel === []) {
    exit('no sé de que va');
}

$oView = new ViewNewPhtml('frontend\\notas\\controller');
foreach ($a_sel as $PersonaSel) {
    if (!is_string($PersonaSel) || $PersonaSel === '') {
        continue;
    }
    $parts = explode('#', $PersonaSel, 2);
    $idNomRaw = $parts[0];
    $id_nom = is_numeric($idNomRaw) ? (int) $idNomRaw : 0;
    $payload = PostRequest::getDataFromUrl('/src/notas/tessera_ver_data', [
        'id_nom' => $id_nom,
    ], false);
    $error = PayloadCoercion::string($payload['error'] ?? '');
    if ($error !== '') {
        echo PostRequest::stripInternalCallProvenance($error);
        return;
    }
    $a_campos = [];
    foreach ($payload as $key => $value) {
        if (is_string($key)) {
            $a_campos[$key] = $value;
        }
    }
    $a_campos['oPosicion'] = $oPosicion;
    $oView->renderizar('tesera_ver.phtml', $a_campos);
}
