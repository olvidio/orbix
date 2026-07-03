<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\cambios\helpers\CambiosPayload;

/**
 * Controlador AJAX HTML: fragmento con el desplegable de fases para el
 * `id_tipo_activ` y `dl_propia` indicados.
 */

use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qobjeto = (string)filter_input(INPUT_POST, 'objeto');
$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');

$data = CambiosPayload::postData(PostRequest::getDataFromUrl('/src/cambios/cambio_usuario_objeto_pref_fases_data', [
    'objeto' => $Qobjeto,
    'id_tipo_activ' => $Qid_tipo_activ,
    'dl_propia' => $Qdl_propia,
]));
$error = PayloadCoercion::string($data['error'] ?? '');
$aFases = NotasFormSupport::desplegableOpciones($data['aFases'] ?? []);

if ($Qobjeto === '') {
    AjaxJsonSupport::html("<span class='alert'>" . _("primero debe elegir un objeto sobre el que mirar los cambios") . "</span>");
}

if ($error !== '') {
    AjaxJsonSupport::html("<span class='alert'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</span>", $error);
}

$oDesplFases = new Desplegable();
$oDesplFases->setBlanco('true');
$oDesplFases->setOpciones($aFases);
$oDesplFases->setNombre('id_fase_ref');

AjaxJsonSupport::html($oDesplFases->desplegable());
