<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;

require_once 'frontend/shared/global_header_front.inc';

$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
$ids_traslados_A = (string)filter_input(INPUT_POST, 'ids_traslados_A');

$data = PostRequest::getDataFromUrl('/src/dbextern/ver_orbix_otradl_datos', [
    'tipo_persona' => $tipo_persona,
    'ids_traslados_A' => $ids_traslados_A,
]);

$a_persona_listas = $data['personas'] ?? [];

// Hash para AJAX trasladar
$url_sincro_trasladar_a = AppUrlConfig::getApiBaseUrl() . '/src/dbextern/sincro_trasladar_a';
$oHash = new HashFront();
$oHash->setUrl($url_sincro_trasladar_a);
$oHash->setCamposForm('dl!id_nom_orbix!tipo_persona');
$h = $oHash->linkSinValParams();

$a_campos = [
    'tipo_persona' => $tipo_persona,
    'a_persona_listas' => $a_persona_listas,
    'url_sincro_trasladar_a' => $url_sincro_trasladar_a,
    'h' => $h,
];

$oView = new ViewNewPhtml('frontend\dbextern\controller');
$oView->renderizar('ver_orbix_otradl.phtml', $a_campos);
