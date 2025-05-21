<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use src\menus\application\repositories\MenuDbRepository;
use src\shared\ViewSrcPhtml;
use src\usuarios\application\repositories\RoleRepository;
use src\usuarios\application\repositories\UsuarioRepository;
use web\Desplegable;
use web\Hash;


// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->setBloque('#ficha'); // antes del recordar
$oPosicion->recordar();

$oCuadros = new \src\menus\domain\PermisoMenu;

$Qfiltro_grupo = (string)filter_input(INPUT_POST, 'filtro_grupo');
$Qnuevo = (string)filter_input(INPUT_POST, 'nuevo');
$Qid_menu = (string)filter_input(INPUT_POST, 'id_menu');

$url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
    . '/src/menus/infrastructure/controllers/lista_meta_menus.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$aOpciones = $data['a_opciones'];

$oDesplMeta = new Desplegable('', $aOpciones, '', true);
$oDesplMeta->setNombre('id_metamenu');


$url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
    . '/src/menus/infrastructure/controllers/lista_grup_menus.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$aOpciones = $data['a_opciones'];

$oDesplGM = new Desplegable('', $aOpciones, '', true);
$oDesplGM->setNombre('gm_new');

$oHash3 = new Hash();
$a_camposHidden = array(
    'filtro_grupo' => $Qfiltro_grupo,
    'nuevo' => 1
);
$oHash3->setArraycamposHidden($a_camposHidden);

$RoleRepository = new RoleRepository();
$aRoles = $RoleRepository->getArrayRoles();
$UsuarioRepository = new UsuarioRepository();

$MenuDbRepository = new MenuDbRepository();
if (!empty($Qid_menu) || !empty($Qnuevo)) {
    if (!empty($Qid_menu)) {
        $oMenuDb = $MenuDbRepository->findById($Qid_menu);
        // para modificar los valores de un menu.
        $oMenuDb->setId_menu($Qid_menu);

        $orden = $oMenuDb->getOrden();
        $orden_txt = implode(',', $orden);
        $menu = $oMenuDb->getMenu();
        $parametros = $oMenuDb->getParametros();
        $id_metamenu = $oMenuDb->getId_metamenu();
        $menu_perm = $oMenuDb->getMenu_perm();
        $id_grupmenu = $oMenuDb->getId_grupmenu();
        $ok = $oMenuDb->isOk();

        $oDesplMeta->setOpcion_sel($id_metamenu);

        $perm_menu = $oCuadros->lista_txt2($menu_perm);
        $a_perm_menu = explode(',', $perm_menu);
        $chk = ($ok) ? 'checked' : '';

    } else {
        $Qid_menu = '';
        $orden_txt = '';
        $menu = '';
        $url = '';
        $parametros = '';
        $perm_menu = '';
        $a_perm_menu = [];
        $menu_perm = 0;
        $chk = '';
    }
    $txt_ok = '';
    $campos_chk = '';

    $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
    $id_role = $oMiUsuario->getId_role();

    //if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'SuperAdmin')) {
    $txt_ok = "  es ok?<input type='checkbox' name='ok' $chk >";
    $campos_chk = 'ok';
    //}

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
        'orden_txt' => $orden_txt,
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

    $oView = new ViewSrcPhtml('frontend\menus\controller');
    $oView->renderizar('menus_get.phtml', $a_campos);
} else {
    // para ver el listado de todos los menus de un grupo
    $oMenuDbs = [];
    if (!empty($Qfiltro_grupo)) {
        $aWhere = array('id_grupmenu' => $Qfiltro_grupo, '_ordre' => 'orden');
        $oMenuDbs = $MenuDbRepository->getMenuDbs($aWhere);
    }

    // para el script
    $url = ConfigGlobal::getWeb() . '/frontend/menus/controller/menus_get.php';
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

    $oView = new ViewSrcPhtml('frontend\menus\controller');
    $oView->renderizar('menus_get_lista.phtml', $a_campos);
}