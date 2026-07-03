<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\ubiscamas\helpers\UbiscamasFormHashCompose;
use frontend\shared\FrontBootstrap;
use frontend\ubiscamas\helpers\UbiscamasPayload;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$campos = array_merge($_GET, $_POST);
$data = UbiscamasPayload::postData(PostRequest::getDataFromUrl('/src/ubiscamas/cama_form_data', $campos));
$hashBlock = UbiscamasFormHashCompose::camaForm($data);
$a_campos = array_merge(
    ['oPosicion' => $oPosicion],
    UbiscamasPayload::camaFormViewFromPayload($data, $hashBlock)
);

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('cama_form.phtml', $a_campos);
