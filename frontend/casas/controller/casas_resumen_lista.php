<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\casas\helpers\CasasPayload;

/**
 * Controlador AJAX HTML: resumen económico de casas (modo periodo y
 * modo anual 5 años). Sucesor de `apps/casas/controller/casas_resumen_ajax.php`.
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$campos = [
    'que' => (string)filter_input(INPUT_POST, 'que'),
    'cdc_sel' => (int)filter_input(INPUT_POST, 'cdc_sel'),
    'id_cdc' => (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
];
$data = CasasPayload::postData(PostRequest::getDataFromUrl('/src/casas/casas_resumen_data', $campos));
$resumen = CasasPayload::resumenListaFromPayload($data);

$a_campos = [
    'a_resumen' => $resumen['a_resumen'],
    'tot' => $resumen['tot'],
    'avisos' => $resumen['avisos'],
    'a_anys' => $resumen['a_anys'],
];

$template = ($resumen['modo'] === 'anual') ? 'casas_resumen_anual.phtml' : 'casas_resumen_periodo.phtml';
$oView = new ViewNewPhtml('frontend\\casas\\controller');
ob_start();
$oView->renderizar($template, $a_campos);
AjaxJsonSupport::html((string) ob_get_clean());
