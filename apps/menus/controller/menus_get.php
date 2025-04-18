<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use menus\model\entity\GestorGrupMenu;
use menus\model\entity\GestorMenuDb;
use menus\model\entity\GestorMetaMenu;
use menus\model\entity\MenuDb;
use usuarios\model\entity\Usuario;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->setBloque('#ficha'); // antes del recordar
$oPosicion->recordar();

$oCuadros = new menus\model\PermisoMenu;

$Qfiltro_grupo = (string)filter_input(INPUT_POST, 'filtro_grupo');
$Qnuevo = (string)filter_input(INPUT_POST, 'nuevo');
$Qid_menu = (string)filter_input(INPUT_POST, 'id_menu');

$oGesMetamenu = new GestorMetaMenu();
$oDesplMeta = $oGesMetamenu->getListaMetamenus();
$oDesplMeta->setNombre('id_metamenu');

$oListaGM = new GestorGrupMenu();

$oDesplGM = $oListaGM->getListaMenus();
$oDesplGM->setNombre('gm_new');

$oHash3 = new Hash();
$a_camposHidden = array(
    'filtro_grupo' => $Qfiltro_grupo,
    'nuevo' => 1
);
$oHash3->setArraycamposHidden($a_camposHidden);

if (!empty($Qid_menu) || !empty($Qnuevo)) {
    if (!empty($Qid_menu)) {
        $oMenuDb = new MenuDb();
        // para modificar los valores de un menu.
        $oMenuDb->setId_menu($Qid_menu);

        $orden = $oMenuDb->getOrden();
        $menu = $oMenuDb->getMenu();
        $parametros = $oMenuDb->getParametros();
        $id_metamenu = $oMenuDb->getId_metamenu();
        $menu_perm = $oMenuDb->getMenu_perm();
        $id_grupmenu = $oMenuDb->getId_grupmenu();
        $ok = $oMenuDb->getOk();

        $oDesplMeta->setOpcion_sel($id_metamenu);
//		$oDesplMeta->setAction('fnjs_lista_menus()');

        $perm_menu = $oCuadros->lista_txt2($menu_perm);
        $a_perm_menu = explode(',', $perm_menu);
        $chk = ($ok) ? 'checked' : '';

    } else {
        $Qid_menu = '';
        $orden = '';
        $menu = '';
        $url = '';
        $parametros = '';
        $perm_menu = '';
        $a_perm_menu = array();
        $menu_perm = 0;
        $chk = '';
    }
    $txt_ok = '';
    $campos_chk = '';
    $oMiusuario = new Usuario(ConfigGlobal::mi_id_usuario());
    if ($oMiusuario->isRole('SuperAdmin')) {
        $txt_ok = "  es ok?<input type='checkbox' name='ok' $chk >";
        $campos_chk = 'ok';
    }

    $oHash = new Hash();
    $oHash->setCamposForm("$campos_chk!orden!txt_menu!id_metamenu!parametros!perm_menu");
    $oHash->setcamposNo($campos_chk);
    $a_camposHidden = array(
        'id_menu' => $Qid_menu,
        'filtro_grupo' => $Qfiltro_grupo,
        'que' => 'guardar'
    );
    $oHash->setArraycamposHidden($a_camposHidden);

    $oHash2 = new Hash();
    $a_camposHidden = array(
        'id_menu' => $Qid_menu,
        'filtro_grupo' => $Qfiltro_grupo,
        'que' => 'del'
    );
    $oHash2->setArraycamposHidden($a_camposHidden);

    $oHash4 = new Hash();
    $a_camposHidden = array(
        'filtro_grupo' => $Qfiltro_grupo
    );
    $oHash4->setArraycamposHidden($a_camposHidden);

    $oHash5 = new Hash();
    $oHash5->setCamposForm("gm_new");
    $a_camposHidden = array(
        'id_menu' => $Qid_menu,
        'filtro_grupo' => $Qfiltro_grupo,
        'que' => 'move'
    );
    $oHash5->setArraycamposHidden($a_camposHidden);

    $oHash6 = new Hash();
    $oHash6->setCamposForm("gm_new");
    $a_camposHidden = array(
        'id_menu' => $Qid_menu,
        'filtro_grupo' => $Qfiltro_grupo,
        'que' => 'copy'
    );
    $oHash6->setArraycamposHidden($a_camposHidden);

    $a_campos = ['oPosicion' => $oPosicion,
        'oHash' => $oHash,
        'orden' => $orden,
        'txt_ok' => $txt_ok,
        'id_menu' => $Qid_menu,
        'menu' => $menu,
        'oDesplMeta' => $oDesplMeta,
        'parametros' => $parametros,
        'oCuadros' => $oCuadros,
        'menu_perm' => $menu_perm,
        'oHash4' => $oHash4,
        'nuevo' => $Qnuevo,
        'oHash2' => $oHash2,
        'oHash3' => $oHash3,
        'oHash5' => $oHash5,
        'oDesplGM' => $oDesplGM,
        'oHash6' => $oHash6,
    ];

    $oView = new ViewPhtml('menus/controller');
    $oView->renderizar('menus_get.phtml', $a_campos);
} else {
    // para ver el listado de todos los menus de un grupo
    $oMenuDbs = array();
    if (!empty($Qfiltro_grupo)) {
        $aWhere = array('id_grupmenu' => $Qfiltro_grupo, '_ordre' => 'orden');
        $oLista = new GestorMenuDb();
        $oMenuDbs = $oLista->getMenuDbs($aWhere);
    }

    // para el script
    $url = ConfigGlobal::getWeb() . '/apps/menus/controller/menus_get.php';
    $oHash2 = new Hash();
    $oHash2->setUrl($url);
    $oHash2->setCamposForm('filtro_grupo!id_menu');
    $h2 = $oHash2->linkSinVal();

    $a_campos = ['oPosicion' => $oPosicion,
        'url' => $url,
        'h2' => $h2,
        'oCuadros' => $oCuadros,
        'oHash3' => $oHash3,
        'oMenuDbs' => $oMenuDbs,
    ];

    $oView = new ViewPhtml('menus/controller');
    $oView->renderizar('menus_get_lista.phtml', $a_campos);
}