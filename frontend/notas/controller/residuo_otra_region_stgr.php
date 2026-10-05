<?php

use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    [],
);

$payload = PostRequest::getDataFromUrl('/src/notas/residuo_otra_region_stgr_data', []);
$pasos = is_array($payload['pasos'] ?? null) ? $payload['pasos'] : [];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('residuo_otra_region_stgr.phtml', [
    'oPosicion' => $oPosicion,
    'pasos' => $pasos,
]);
