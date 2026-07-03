<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\ListNavSupport;

/**
 * Fragmento HTML con la lista de centros y sus actividades en un periodo.
 *
 * Se invoca via AJAX desde `actividades_centro_que` (cuando Qque ∈ {crt, cv})
 * y el resultado (HTML) se inyecta directamente en el DOM.
 *
 * La logica se ha trasladado a `src\actividades\application\ListaCentrosActivDatos`
 * y se consume via PostRequest.
 *
 * Migrado desde frontend/actividades/controller/lista_centros_activ.php.
 *
 * @package    delegacion
 * @subpackage actividades
 */

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
ListNavSupport::bootRecordar($oPosicion);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::buildReturnParametrosFromPost());


$Qid_ctr_num = (integer)filter_input(INPUT_POST, 'id_ctr_num');
$Qa_id_ctr = (array)filter_input(INPUT_POST, 'id_ctr', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');

$data = PostRequest::getDataFromUrl('/src/actividades/lista_centros_activ_datos', [
    'id_ctr_num' => $Qid_ctr_num,
    'id_ctr' => $Qa_id_ctr,
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
]);

AjaxJsonSupport::html(PayloadCoercion::string($data['html'] ?? ''));
