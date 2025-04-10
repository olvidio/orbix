<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\DesplegableArray;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************


// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qquien = (string)filter_input(INPUT_POST, 'quien');

$Qscroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
// Hay que usar isset y empty porque puede tener el valor =0.
// Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $a_sel = $oPosicion2->getParametro('id_sel');
            if (!empty($a_sel)) {
                $Qid_usuario = (integer)strtok($a_sel[0], "#");
            } else {
                $Qid_usuario = $oPosicion2->getParametro('id_usuario');
                $Qquien = $oPosicion2->getParametro('quien');
            }
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
} elseif (!empty($a_sel)) { //vengo de un checkbox
    $Qque = (string)filter_input(INPUT_POST, 'que');
    if ($Qque !== 'del_grupmenu') { //En el caso de venir de borrar un grupmenu, no hago nada
        $Qid_usuario = (integer)strtok($a_sel[0], "#");
        // el scroll id es de la página anterior, hay que guardarlo allí
        $oPosicion->addParametro('id_sel', $a_sel, 1);
        $Qscroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
        $oPosicion->addParametro('scroll_id', $Qscroll_id, 1);
    }
}
$oPosicion->setParametros(array('id_usuario' => $Qid_usuario), 1);

//////////////////////// Usuario o Grupo ///////////////////////////////////////////////////
$url_usuario_form_backend = Hash::link(ConfigGlobal::getWeb()
    . '/apps/usuarios/controller/usuario_form.php'
);

$oHash = new Hash();
$oHash->setUrl($url_usuario_form_backend);
$oHash->setArrayCamposHidden(
    ['id_usuario' => $Qid_usuario,
        'quien' => $Qquien
    ]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_usuario_form_backend, $hash_params);

$a_campos = $data['a_campos'];

$txt_guardar = _("guardar datos usuario");
$txt_eliminar = _("¿Está seguro que desea quitar este permiso?");


// recomponer los campos desplegables.
$oDesplRoles = new Desplegable();
$oDesplRoles->import($a_campos['oDesplRoles']);
$a_campos['oDesplRoles'] = $oDesplRoles;

$oSelects = new DesplegableArray();
$oSelects->import($a_campos['oSelects']);
$a_campos['oSelects'] = $oSelects;

$oHash = new Hash();
$oHash->import($a_campos['oHash']);
$a_campos['oHash'] = $oHash;
// añadir Posicion
$a_campos['oPosicion'] = $oPosicion;
$a_campos['txt_guardar'] = $txt_guardar;
$a_campos['txt_eliminar'] = $txt_eliminar;
$a_campos['url_usuario_guardar'] = Hash::link(ConfigGlobal::getWeb()
    . '/apps/usuarios/controller/usuario_guardar.php'
);

//$a_campos['url_usuario_ajax'] = '';
//$url_usuario_ajax = ConfigGlobal::getWeb() . '/apps/usuarios/controller/usuario_ajax.php';

$url = ConfigGlobal::getWeb() . '/frontend/usuarios/controller/usuario_grupo_lst.php';
$oHash1 = new Hash();
$oHash1->setUrl($url);
$oHash1->setCamposForm('id_usuario');
$oHash1->setCamposNo('scroll_id');
$h_lst = $oHash1->linkSinVal();
$a_campos['h_lst'] = $h_lst;

$url = ConfigGlobal::getWeb() . '/frontend/usuarios/controller/usuario_grupo_del_lst.php';
$oHash2 = new Hash();
$oHash2->setUrl($url);
$oHash2->setCamposForm('id_usuario');
$oHash2->setCamposNo('scroll_id');
$h_del_lst = $oHash2->linkSinVal();
$a_campos['h_del_lst'] = $h_del_lst;

$url_usuario_update = ConfigGlobal::getWeb() . '/apps/usuarios/controller/usuario_check_pwd.php';
$oHash3 = new Hash();
$oHash3->setUrl($url_usuario_update);
$oHash3->setCamposForm('id_usuario!usuario!password');
$h_pwd = $oHash3->linkSinVal();
$a_campos['h_pwd'] = $h_pwd;

$oView = new ViewPhtml('../frontend/usuarios/controller');
$oView->renderizar('usuario_form.phtml', $a_campos);

//////////////////////// Grupos del usuario ///////////////////////////////////////////////////
$url_usuario_form_backend = Hash::link(ConfigGlobal::getWeb()
    . '/apps/usuarios/controller/usuario_info.php'
);

$oHash = new Hash();
$oHash->setUrl($url_usuario_form_backend);
$oHash->setArrayCamposHidden(
    ['id_usuario' => $Qid_usuario,
    ]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_usuario_form_backend, $hash_params);

$a_campos['grupos_txt'] = $data['grupos_txt'];

$oView = new ViewPhtml('../frontend/usuarios/controller');
$oView->renderizar('usuario_grupo.phtml', $a_campos);


//////////// Permisos en actividades ////////////
if (ConfigGlobal::is_app_installed('procesos')) {
    $url = Hash::link(ConfigGlobal::getWeb()
        . '/frontend/usuarios/controller/perm_activ_lista.php'
    );

    $oHash = new Hash();
    $oHash->setUrl($url);
    $oHash->setArrayCamposHidden(['id_usuario' => $Qid_usuario, 'olvidar' => 1]);
    $hash_params = $oHash->getArrayCampos();

    echo PostRequest::getContent($url, $hash_params);
}

//////////// Condiciones para los avisos de cambios ////////////
if (ConfigGlobal::is_app_installed('cambios')) {
    $url_avisos = Hash::link(ConfigGlobal::getWeb() . '/frontend/cambios/controller/usuario_form_avisos.php?' . http_build_query(array('quien' => 'usuario', 'id_usuario' => $Qid_usuario)));

    $a_campos['url_avisos'] = $url_avisos;

    $oView = new ViewPhtml('../frontend/usuarios/controller');
    $oView->renderizar('usuario_form_avisos.phtml', $a_campos);
}
