<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// lista de menus disponibles:
$url_lista_backend = Hash::cmdSinParametros(ConfigGlobal::getWeb()
    . '/src/menus/infrastructure/controllers/lista_templates.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden([]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_opciones = $data['a_opciones'];
$oDesplTemplates = new Desplegable('id_template_menu', $a_opciones, '', true);

$url = ConfigGlobal::getWeb() . '/src/menus/infrastructure/controllers/menus_importar.php';
$oHash = new Hash();
$oHash->setUrl($url);
$oHash->setCamposForm('id_template_menu');

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oDesplTemplates' => $oDesplTemplates,
];

$oView = new ViewNewPhtml('frontend\menus\controller');
$oView->renderizar('menus_importar_form.phtml', $a_campos);
