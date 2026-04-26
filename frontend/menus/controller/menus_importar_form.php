<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// lista de menus disponibles:
$url_backend = '/src/menus/lista_templates';
$data = PostRequest::getDataFromUrl($url_backend);

$a_opciones = $data['a_opciones'];
$oDesplTemplates = new Desplegable('id_template_menu', $a_opciones, '', true);

$url = AppUrlConfig::getApiBaseUrl() . '/src/menus/menus_importar';
$oHash = new Hash();
$oHash->setUrl($url);
$oHash->setCamposForm('id_template_menu');

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oDesplTemplates' => $oDesplTemplates,
];

$oView = new ViewNewPhtml('frontend\menus\controller');
$oView->renderizar('menus_importar_form.phtml', $a_campos);
