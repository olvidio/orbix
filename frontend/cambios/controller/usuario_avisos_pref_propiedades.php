<?php
/**
 * Controlador AJAX HTML: fragmento con la tabla de propiedades seleccionables
 * para el `CambioUsuarioObjetoPref` indicado.
 */

use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/cambios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

FrontBootstrap::boot();
$Qobjeto = (string)filter_input(INPUT_POST, 'objeto');
$Qid_item_usuario_objeto = (int)filter_input(INPUT_POST, 'id_item_usuario_objeto');

$data = cambios_post_data(PostRequest::getDataFromUrl('/src/cambios/cambio_usuario_objeto_pref_propiedades_data', [
    'objeto' => $Qobjeto,
    'id_item_usuario_objeto' => $Qid_item_usuario_objeto,
]));
$propiedades = cambios_propiedades_rows($data['propiedades'] ?? []);

$scamposForm = '';
foreach ($propiedades as $p) {
    $nomProp = cambios_propiedad_nom_prop($p);
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

ajax_json_render_phtml('frontend\\cambios\\controller', 'usuario_avisos_pref_propiedades.phtml', $a_campos);
