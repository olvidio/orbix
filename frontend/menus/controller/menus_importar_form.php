<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once __DIR__ . '/../helpers/menus_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

// lista de menus disponibles:
$url_backend = '/src/menus/lista_templates';
$data = PostRequest::getDataFromUrl($url_backend);

$a_opciones = notas_desplegable_opciones($data['a_opciones'] ?? []);
$oDesplTemplates = new Desplegable('id_template_menu', $a_opciones, '', true);

$url = AppUrlConfig::getApiBaseUrl() . '/src/menus/menus_importar';
$oHash = new HashFront();
$oHash->setUrl($url);
$oHash->setCamposForm('id_template_menu');

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oDesplTemplates' => $oDesplTemplates,
];

$oView = new ViewNewPhtml('frontend\menus\controller');
$oView->renderizar('menus_importar_form.phtml', $a_campos);
