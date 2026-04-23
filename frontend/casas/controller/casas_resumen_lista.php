<?php
/**
 * Controlador AJAX HTML: resumen económico de casas (modo periodo y
 * modo anual 5 años). Sucesor de `apps/casas/controller/casas_resumen_ajax.php`.
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

$campos = [
    'que' => (string)filter_input(INPUT_POST, 'que'),
    'cdc_sel' => (int)filter_input(INPUT_POST, 'cdc_sel'),
    'id_cdc' => (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
];
$data = PostRequest::getDataFromUrl('/src/casas/casas_resumen_data', $campos);
$payload = is_array($data) ? $data : [];

$modo = (string)($payload['modo'] ?? 'periodo');
$a_resumen = (array)($payload['a_resumen'] ?? []);
$tot = (array)($payload['tot'] ?? []);
$avisos = (array)($payload['avisos'] ?? []);
$a_anys = (array)($payload['a_anys'] ?? []);

$a_campos = [
    'a_resumen' => $a_resumen,
    'tot' => $tot,
    'avisos' => $avisos,
    'a_anys' => $a_anys,
];

$template = ($modo === 'anual') ? 'casas_resumen_anual.phtml' : 'casas_resumen_periodo.phtml';
$oView = new ViewNewPhtml('frontend\\casas\\controller');
$oView->renderizar($template, $a_campos);
