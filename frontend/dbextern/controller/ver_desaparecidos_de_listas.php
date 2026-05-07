<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use src\shared\config\ConfigGlobal;

require_once 'frontend/shared/global_header_front.inc';

$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
$ids_desaparecidos_de_listas = (string)filter_input(INPUT_POST, 'ids_desaparecidos_de_listas');

$data = PostRequest::getDataFromUrl('/src/dbextern/ver_desaparecidos_de_listas_datos', [
    'tipo_persona' => $tipo_persona,
    'ids_desaparecidos_de_listas' => $ids_desaparecidos_de_listas,
]);

$a_persona_orbix = $data['personas'] ?? [];

// Hash para AJAX baja
$url_sincro_baja = ConfigGlobal::getWeb() . '/src/dbextern/sincro_baja';
$oHash = new HashFront();
$oHash->setUrl($url_sincro_baja);
$oHash->setCamposForm('id_nom_orbix!tipo_persona');
$h = $oHash->linkSinValParams();

$a_campos = [
    'tipo_persona' => $tipo_persona,
    'a_persona_orbix' => $a_persona_orbix,
    'url_sincro_baja' => $url_sincro_baja,
    'h' => $h,
];

$oView = new ViewNewPhtml('frontend/dbextern/controller');
$oView->renderizar(__FILE__, $a_campos);
