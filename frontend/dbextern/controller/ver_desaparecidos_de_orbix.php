<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use src\shared\config\ConfigGlobal;

require_once 'frontend/shared/global_header_front.inc';

$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
$ids_desaparecidos_de_orbix = (string)filter_input(INPUT_POST, 'ids_desaparecidos_de_orbix');

$data = PostRequest::getDataFromUrl('/src/dbextern/ver_desaparecidos_de_orbix_datos', [
    'tipo_persona' => $tipo_persona,
    'ids_desaparecidos_de_orbix' => $ids_desaparecidos_de_orbix,
]);

$a_persona_listas = $data['personas'] ?? [];

// Hash para AJAX desunir
$url_sincro_desunir = ConfigGlobal::getWeb() . '/src/dbextern/sincro_desunir';
$oHash = new HashFront();
$oHash->setUrl($url_sincro_desunir);
$oHash->setCamposForm('id_nom_listas!tipo_persona');
$h = $oHash->linkSinValParams();

$a_campos = [
    'tipo_persona' => $tipo_persona,
    'a_persona_listas' => $a_persona_listas,
    'url_sincro_desunir' => $url_sincro_desunir,
    'h' => $h,
];

$oView = new ViewNewPhtml('frontend/dbextern/controller');
$oView->renderizar(__FILE__, $a_campos);
