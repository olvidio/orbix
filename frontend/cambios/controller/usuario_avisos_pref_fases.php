<?php
/**
 * Controlador AJAX HTML: fragmento con el desplegable de fases para el
 * `id_tipo_activ` y `dl_propia` indicados.
 *
 * Sucesor de la rama `av_fases` del dispatcher legacy
 * `apps/cambios/controller/usuario_avisos_pref_ajax.php`. Consume el endpoint
 * JSON `/src/cambios/cambio_usuario_objeto_pref_fases_data`.
 */

use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;

require_once 'frontend/shared/global_header_front.inc';

$Qobjeto = (string)filter_input(INPUT_POST, 'objeto');
$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');

$data = PostRequest::getDataFromUrl('/src/cambios/cambio_usuario_objeto_pref_fases_data', [
    'objeto' => $Qobjeto,
    'id_tipo_activ' => $Qid_tipo_activ,
    'dl_propia' => $Qdl_propia,
]);
$payload = is_array($data) ? $data : [];
$error = (string)($payload['error'] ?? '');
$aFases = (array)($payload['aFases'] ?? []);

if ($Qobjeto === '') {
    echo "<span class='alert'>" . _("primero debe elegir un objeto sobre el que mirar los cambios") . "</span>";
    return;
}
if ($error !== '') {
    echo "<span class='alert'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</span>";
    return;
}

$oDesplFases = new Desplegable();
$oDesplFases->setBlanco('true');
$oDesplFases->setOpciones($aFases);
$oDesplFases->setNombre('id_fase_ref');

echo $oDesplFases->desplegable();
