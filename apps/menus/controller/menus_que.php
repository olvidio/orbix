<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use menus\model\entity\GestorGrupMenu;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qfiltro_grupo = (string)filter_input(INPUT_POST, 'filtro_grupo');

$oLista = new GestorGrupMenu();

$oDespl = $oLista->getListaMenus();
$oDespl->setOpcion_sel($Qfiltro_grupo);
$oDespl->setAction('fnjs_lista_menus()');
$oDespl->setNombre('filtro_grupo');

$url = ConfigGlobal::getWeb() . '/apps/menus/controller/menus_get.php';
$oHash1 = new Hash();
$oHash1->setUrl($url);
$oHash1->setCamposForm('filtro_grupo');
$h1 = $oHash1->linkSinVal();

$a_campos = ['url' => $url,
    'h1' => $h1,
    'oDespl' => $oDespl,
];

$oView = new ViewPhtml('menus/controller');
$oView->renderizar('menus_que.phtml', $a_campos);