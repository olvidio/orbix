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

$Qnuevo = \frontend\shared\helpers\PayloadCoercion::string($campos['nuevo'] ?? '');
$Qid_habitacion = \frontend\shared\helpers\PayloadCoercion::string($campos['id_habitacion'] ?? '');
if ($Qnuevo === '') {
    ListNavSupport::restoreSelectionFromStackPost();
}

$navIdentity = $Qnuevo === '' && $Qid_habitacion !== '' ? ['id_habitacion' => $Qid_habitacion] : [];
$navState = ListNavSupport::mergeSelectionIntoReturnParametros(
    ListNavSupport::buildReturnParametrosFromPost(),
    ListNavSupport::idSelFromPost(),
    ListNavSupport::scrollIdFromPost(),
);
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $navIdentity,
    $navState,
);
ListNavSupport::syncNavStateAt(
    $oPosicion,
    1,
    ListNavSupport::buildSelectionStatePatchFromPost(),
);


$data = UbiscamasPayload::postData(PostRequest::getDataFromUrl('/src/ubiscamas/habitacion_form_data', $campos));
$hashBlock = UbiscamasFormHashCompose::habitacionForm($data);

$a_campos = array_merge(
    ['oPosicion' => $oPosicion],
    UbiscamasPayload::habitacionFormViewFromPayload($data, $hashBlock)
);

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('habitacion_form.phtml', $a_campos);
