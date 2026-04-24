<?php
/**
 * Controlador AJAX HTML: fragmento con la tabla de propiedades seleccionables
 * para el `CambioUsuarioObjetoPref` indicado.
 *
 * Sucesor de la rama `propiedades` del dispatcher legacy
 * `apps/cambios/controller/usuario_avisos_pref_ajax.php`. Consume el endpoint
 * JSON `/src/cambios/cambio_usuario_objeto_pref_propiedades_data` y renderiza
 * `usuario_avisos_pref_propiedades.phtml`.
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$Qobjeto = (string)filter_input(INPUT_POST, 'objeto');
$Qid_item_usuario_objeto = (int)filter_input(INPUT_POST, 'id_item_usuario_objeto');

$data = PostRequest::getDataFromUrl('/src/cambios/cambio_usuario_objeto_pref_propiedades_data', [
    'objeto' => $Qobjeto,
    'id_item_usuario_objeto' => $Qid_item_usuario_objeto,
]);
$payload = is_array($data) ? $data : [];
$propiedades = (array)($payload['propiedades'] ?? []);

$scamposForm = '';
foreach ($propiedades as $p) {
    $id_cond = $Qobjeto . '_' . $p['nom_prop'] . '_cond';
    $td_item = $Qobjeto . '_' . $p['nom_prop'] . '_item';
    $scamposForm .= $id_cond . '!' . $td_item . '!';
}

$oHash = new Hash();
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

$oView = new ViewNewPhtml('frontend\\cambios\\controller');
$oView->renderizar('usuario_avisos_pref_propiedades.phtml', $a_campos);
