<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Desplegable;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->setBloque('#ficha'); // antes del recordar
$oPosicion->recordar();

$Qfiltro_grupo = (string)filter_input(INPUT_POST, 'filtro_grupo');
$Qnuevo = (string)filter_input(INPUT_POST, 'nuevo');
$Qid_menu = (string)filter_input(INPUT_POST, 'id_menu');

$url_backend = '/src/menus/lista_meta_menus';
$data = PostRequest::getDataFromUrl($url_backend);

$aOpciones = $data['a_opciones'];

$oDesplMeta = new Desplegable('', $aOpciones, '', true);
$oDesplMeta->setNombre('id_metamenu');


$url_backend = '/src/menus/grupmenu_lista';
$data = PostRequest::getDataFromUrl($url_backend);

$aOpciones = $data['a_lista'];

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

$perm_menu_bit_map = [];
if (isset($pageData['perm_menu_bit_map']) && is_array($pageData['perm_menu_bit_map'])) {
    $perm_menu_bit_map = $pageData['perm_menu_bit_map'];
}

if (($pageData['mode'] ?? '') === 'edit') {
    $Qid_menu = (string)($pageData['id_menu'] ?? '');
    $orden_txt = (string)($pageData['orden_txt'] ?? '');
    $menu = (string)($pageData['menu'] ?? '');
    $parametros = (string)($pageData['parametros'] ?? '');
    $id_metamenu = $pageData['id_metamenu'] ?? null;
    $menu_perm = $pageData['menu_perm'] ?? 0;
    $txt_ok = (string)($pageData['txt_ok'] ?? '');
    $campos_chk = (string)($pageData['campos_chk'] ?? 'ok');

    if ($id_metamenu !== null) {
        $oDesplMeta->setOpcion_sel((int)$id_metamenu);
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

    $oView = new ViewNewPhtml('frontend\menus\controller');
    $oView->renderizar('menus_get.phtml', $a_campos);
} else {
    $menuRows = (array)($pageData['menu_rows'] ?? []);

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

    $oView = new ViewNewPhtml('frontend\menus\controller');
    $oView->renderizar('menus_get_lista.phtml', $a_campos);
}
