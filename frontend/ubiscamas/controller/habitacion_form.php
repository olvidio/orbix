<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\ubiscamas\helpers\UbiscamasFormHashCompose;
use frontend\shared\FrontBootstrap;
use frontend\ubiscamas\helpers\UbiscamasPayload;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int)filter_input(INPUT_POST, 'refresh');

$campos = array_merge($_GET, $_POST);

// Resolver estado de navegación aquí (frontend): recortar hacia delante desde $stack.
// Sólo tiene sentido si no se está creando una habitación nueva.
$Qnuevo = \frontend\shared\helpers\PayloadCoercion::string($campos['nuevo'] ?? '');
$stackFromPost = isset($campos['stack']) ? (string) filter_var($campos['stack'], FILTER_SANITIZE_NUMBER_INT) : '';
if ($Qnuevo === '' && $stackFromPost !== '' && $oPosicion->goStack($stackFromPost)) {
    $oPosicion->olvidar($stackFromPost);
}

\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion, $Qrefresh);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::mergeSelectionIntoReturnParametros(\frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost(), \frontend\shared\helpers\ListNavSupport::idSelFromPost(), \frontend\shared\helpers\ListNavSupport::scrollIdFromPost()));


$data = UbiscamasPayload::postData(PostRequest::getDataFromUrl('/src/ubiscamas/habitacion_form_data', $campos));
$hashBlock = UbiscamasFormHashCompose::habitacionForm($data);

$a_campos = array_merge(
    ['oPosicion' => $oPosicion],
    UbiscamasPayload::habitacionFormViewFromPayload($data, $hashBlock)
);

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('habitacion_form.phtml', $a_campos);
