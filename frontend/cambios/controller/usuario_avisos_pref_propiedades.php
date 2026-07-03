<?php

use frontend\shared\helpers\AjaxJsonSupport;

/**
 * Controlador AJAX HTML: fragmento con la tabla de propiedades seleccionables
 * para el `CambioUsuarioObjetoPref` indicado.
 */

use frontend\cambios\helpers\CambiosPayload;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qobjeto = (string)filter_input(INPUT_POST, 'objeto');
$Qid_item_usuario_objeto = (int)filter_input(INPUT_POST, 'id_item_usuario_objeto');

$data = CambiosPayload::postData(PostRequest::getDataFromUrl('/src/cambios/cambio_usuario_objeto_pref_propiedades_data', [
    'objeto' => $Qobjeto,
    'id_item_usuario_objeto' => $Qid_item_usuario_objeto,
]));
$propiedades = CambiosPayload::propiedadesRows($data['propiedades'] ?? []);

$scamposForm = '';
foreach ($propiedades as $p) {
    $nomProp = CambiosPayload::propiedadNomProp($p);
    $id_cond = $Qobjeto . '_' . $nomProp . '_cond';
    $td_item = $Qobjeto . '_' . $nomProp . '_item';
    $scamposForm .= $id_cond . '!' . $td_item . '!';
}

$oHash = new HashFront();
$oHash->setCamposForm($scamposForm . '!salida!id_item_usuario_objeto_prop');
$oHash->setArrayCamposHidden(['objeto_prop' => $Qobjeto]);
$oHash->setCamposChk($Qobjeto);
$oHash->setCamposNo('casas!test');

$a_campos = [
    'Qobjeto' => $Qobjeto,
    'Qid_item_usuario_objeto' => $Qid_item_usuario_objeto,
    'propiedades' => $propiedades,
    'oHash' => $oHash,
];

AjaxJsonSupport::renderPhtml('frontend\\cambios\\controller', 'usuario_avisos_pref_propiedades.phtml', $a_campos);
