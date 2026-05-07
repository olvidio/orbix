<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;

require_once 'frontend/shared/global_header_front.inc';

$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
$ids_traslados = (string)filter_input(INPUT_POST, 'ids_traslados');

$data = PostRequest::getDataFromUrl('/src/dbextern/ver_traslados_datos', [
    'tipo_persona' => $tipo_persona,
    'ids_traslados' => $ids_traslados,
]);

$a_persona_orbix = $data['personas'] ?? [];

// Hash para AJAX trasladar
$url_sincro_trasladar = AppUrlConfig::getApiBaseUrl() . '/src/dbextern/sincro_trasladar';
$oHash = new HashFront();
$oHash->setUrl($url_sincro_trasladar);
$oHash->setCamposForm('dl!id_nom_orbix!tipo_persona');
$h = $oHash->linkSinValParams();

$a_campos = [
    'tipo_persona' => $tipo_persona,
    'a_persona_orbix' => $a_persona_orbix,
    'url_sincro_trasladar' => $url_sincro_trasladar,
    'h' => $h,
];

$oView = new ViewNewPhtml('frontend\dbextern\controller');
$oView->renderizar(__FILE__, $a_campos);
