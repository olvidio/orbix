<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once __DIR__ . '/../helpers/menus_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->setBloque('#ficha'); // antes del recordar
list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$Qfiltro_grupo = (string)filter_input(INPUT_POST, 'filtro_grupo');
$Qnuevo = (string)filter_input(INPUT_POST, 'nuevo');
$Qid_menu = (string)filter_input(INPUT_POST, 'id_menu');

$url_backend = '/src/menus/lista_meta_menus';
$data = PostRequest::getDataFromUrl($url_backend);

$aOpciones = notas_desplegable_opciones($data['a_opciones'] ?? []);

$oDesplMeta = new Desplegable('', $aOpciones, '', true);
$oDesplMeta->setNombre('id_metamenu');


$url_backend = '/src/menus/grupmenu_lista';
$data = PostRequest::getDataFromUrl($url_backend);

$aOpciones = notas_desplegable_opciones($data['a_lista'] ?? []);

$oDesplGM = new Desplegable('', $aOpciones, '', true);
$oDesplGM->setNombre('gm_new');

$oHash3 = new HashFront();
$a_camposHidden = array(
    'filtro_grupo' => $Qfiltro_grupo,
    'nuevo' => 1
);
$oHash3->setArraycamposHidden($a_camposHidden);

$pageData = PostRequest::getDataFromUrl('/src/menus/menus_get_page_data', [
    'filtro_grupo' => $Qfiltro_grupo,
    'nuevo' => $Qnuevo,
    'id_menu' => $Qid_menu,
]);

$page = menus_get_page_from_payload($pageData);
$perm_menu_bit_map = menus_perm_menu_bit_map($pageData['perm_menu_bit_map'] ?? []);

if ($page['mode'] === 'edit') {
    $Qid_menu = $page['id_menu'];
    $orden_txt = $page['orden_txt'];
    $menu = $page['menu'];
    $parametros = $page['parametros'];
    $menu_perm = $page['menu_perm'];
    $txt_ok = $page['txt_ok'];
    $campos_chk = $page['campos_chk'];

    if ($page['id_metamenu'] !== '') {
        $oDesplMeta->setOpcion_sel($page['id_metamenu']);
    }

    $oHash = new HashFront();
    $oHash->setCamposForm("$campos_chk!orden!txt_menu!id_metamenu!parametros!perm_menu");
    $oHash->setcamposNo($campos_chk);
    $a_camposHidden = array(
        'id_menu' => $Qid_menu,
        'filtro_grupo' => $Qfiltro_grupo,
        'que' => 'guardar'
    );
    $oHash->setArraycamposHidden($a_camposHidden);

    $oHash2 = new HashFront();
    $a_camposHidden = array(
        'id_menu' => $Qid_menu,
        'filtro_grupo' => $Qfiltro_grupo,
        'que' => 'del'
    );
    $oHash2->setArraycamposHidden($a_camposHidden);

    $oHash4 = new HashFront();
    $a_camposHidden = array(
        'filtro_grupo' => $Qfiltro_grupo
    );
    $oHash4->setArraycamposHidden($a_camposHidden);

    $oHash5 = new HashFront();
    $oHash5->setCamposForm("gm_new");
    $a_camposHidden = array(
        'id_menu' => $Qid_menu,
        'filtro_grupo' => $Qfiltro_grupo,
        'que' => 'move'
    );
    $oHash5->setArraycamposHidden($a_camposHidden);

    $oHash6 = new HashFront();
    $oHash6->setCamposForm("gm_new");
    $a_camposHidden = array(
        'id_menu' => $Qid_menu,
        'filtro_grupo' => $Qfiltro_grupo,
        'que' => 'copy'
    );
    $oHash6->setArraycamposHidden($a_camposHidden);

    $a_campos = ['oPosicion' => $oPosicion,
        'oHash' => $oHash,
        'orden_txt' => $orden_txt,
        'txt_ok' => $txt_ok,
        'id_menu' => $Qid_menu,
        'menu' => $menu,
        'oDesplMeta' => $oDesplMeta,
        'parametros' => $parametros,
        'perm_menu_bit_map' => $perm_menu_bit_map,
        'menu_perm' => $menu_perm,
        'oHash4' => $oHash4,
        'nuevo' => $Qnuevo,
        'oHash2' => $oHash2,
        'oHash3' => $oHash3,
        'oHash5' => $oHash5,
        'oDesplGM' => $oDesplGM,
        'oHash6' => $oHash6,
    ];

    ajax_json_render_phtml('frontend\menus\controller', 'menus_get.phtml', $a_campos);
} else {
    $menuRows = $page['menu_rows'];

    $url = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/menus/controller/menus_get.php';
    $oHash2 = new HashFront();
    $oHash2->setUrl($url);
    $oHash2->setCamposForm('filtro_grupo!id_menu');
    $h2 = $oHash2->linkSinValParams();

    $a_campos = ['oPosicion' => $oPosicion,
        'url' => $url,
        'h2' => $h2,
        'perm_menu_bit_map' => $perm_menu_bit_map,
        'oHash3' => $oHash3,
        'menuRows' => $menuRows,
    ];

    ajax_json_render_phtml('frontend\menus\controller', 'menus_get_lista.phtml', $a_campos);
}
