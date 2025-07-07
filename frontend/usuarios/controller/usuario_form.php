<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
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
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
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
$url_usuario_form_backend = Hash::cmdSinParametros(ConfigGlobal::getWeb()
    . '/src/usuarios/infrastructure/controllers/usuario_form.php'
);

$oHash = new Hash();
$oHash->setUrl($url_usuario_form_backend);
$oHash->setArrayCamposHidden(
    ['id_usuario' => $Qid_usuario,
        'quien' => $Qquien
    ]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_usuario_form_backend, $hash_params);

$a_campos_src = $data['a_campos'];

$txt_guardar = _("guardar datos usuario");
$txt_eliminar = _("¿Está seguro que desea quitar este permiso?");


// recomponer los campos desplegables.
$aOpcionesRoles = $a_campos_src['aOpcionesRoles'];
$id_role = $a_campos_src['id_role'];
$oDesplRoles = new Desplegable('id_role', $aOpcionesRoles, $id_role, true);
$a_campos['oDesplRoles'] = $oDesplRoles;

if (!empty($a_campos_src['aDataDespl'])) {
    $tipo = $a_campos_src['aDataDespl']['tipo'] = 'simple';
    if ($tipo === 'simple') {
        $oDesplArrayCtrCasas = new Desplegable();
    } else {
        $oDesplArrayCtrCasas = new DesplegableArray();
        $oDesplArrayCtrCasas->setNombre($a_campos_src['aDataDespl']['accionConjunto']);
    }
    $oDesplArrayCtrCasas->setNombre($a_campos_src['aDataDespl']['nom']);
    $oDesplArrayCtrCasas->setOpciones($a_campos_src['aDataDespl']['aOpciones']);
    $oDesplArrayCtrCasas->setOpcion_sel($a_campos_src['aDataDespl']['opcion_sel']);
    $oDesplArrayCtrCasas->setBlanco($a_campos_src['aDataDespl']['blanco']);
} else {
    $oDesplArrayCtrCasas = new Desplegable();
}
$a_campos['oDesplArrayCtrCasas'] = $oDesplArrayCtrCasas;

$oHash = new Hash();
$camposMas = $a_campos_src['camposMas'];
$camposForm = 'que!usuario!nom_usuario!password!email!id_role';
$camposForm = !empty($camposMas) ? $camposForm . '!' . $camposMas : $camposForm;
$oHash->setCamposForm($camposForm);
$oHash->setcamposNo('password!id_ctr!id_nom!casas!cambio_password!has_2fa');
$a_camposHidden = array(
    'id_usuario' => $Qid_usuario,
    'quien' => $Qquien
);
$oHash->setArraycamposHidden($a_camposHidden);
$a_campos['oHash'] = $oHash;


// añadir Posicion
$a_campos['oPosicion'] = $oPosicion;
$a_campos['txt_guardar'] = $txt_guardar;
$a_campos['txt_eliminar'] = $txt_eliminar;
$a_campos['url_usuario_guardar'] = Hash::link(ConfigGlobal::getWeb()
    . '/src/usuarios/infrastructure/controllers/usuario_guardar.php'
);

//$a_campos['url_usuario_ajax'] = '';
//$url_usuario_ajax = ConfigGlobal::getWeb() . '/src/usuarios/infrastructure/controllers/usuario_ajax.php';


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

$url_usuario_update = ConfigGlobal::getWeb() . '/src/usuarios/infrastructure/controllers/usuario_check_pwd.php';
$oHash3 = new Hash();
$oHash3->setUrl($url_usuario_update);
$oHash3->setCamposForm('id_usuario!usuario!password');
$h_pwd = $oHash3->linkSinVal();
$a_campos['h_pwd'] = $h_pwd;

$a_campos['id_usuario'] = $a_campos_src['id_usuario'];
$a_campos['usuario'] = $a_campos_src['usuario'];
$a_campos['quien'] = $a_campos_src['quien'];
$a_campos['pau'] = $a_campos_src['pau'];
$a_campos['nom_usuario'] = $a_campos_src['nom_usuario'];
$a_campos['email'] = $a_campos_src['email'];
$a_campos['chk_cambio_password'] = $a_campos_src['chk_cambio_password'];
$a_campos['chk_has_2fa'] = $a_campos_src['chk_has_2fa'];
$a_campos['obj'] = $a_campos_src['obj'];


$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('usuario_form.phtml', $a_campos);

// los nuevos no tienen lo que sigue.
if (!empty($Qid_usuario)) {
    //////////////////////// Grupos del usuario ///////////////////////////////////////////////////
    $url_usuario_form_backend = Hash::cmdSinParametros(ConfigGlobal::getWeb()
        . '/src/usuarios/infrastructure/controllers/usuario_info.php'
    );

    $oHash = new Hash();
    $oHash->setUrl($url_usuario_form_backend);
    $oHash->setArrayCamposHidden(
        ['id_usuario' => $Qid_usuario,
        ]);
    $hash_params = $oHash->getArrayCampos();

    $data = PostRequest::getData($url_usuario_form_backend, $hash_params);

    $a_campos['grupos_txt'] = $data['grupos_txt'];

    $oView = new ViewNewPhtml('frontend\usuarios\controller');
    $oView->renderizar('usuario_grupo.phtml', $a_campos);


    //////////// Permisos en actividades ////////////
    if (ConfigGlobal::is_app_installed('procesos')) {
        $url = Hash::cmdSinParametros(ConfigGlobal::getWeb()
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
        $url_avisos = Hash::cmdSinParametros(ConfigGlobal::getWeb()
            . '/frontend/cambios/controller/usuario_form_avisos.php?'
            . http_build_query(['quien' => 'usuario', 'id_usuario' => $Qid_usuario])
        );

        $oHash = new Hash();
        $oHash->setUrl($url);
        $oHash->setArrayCamposHidden(['id_usuario' => $Qid_usuario, 'quien' => 'usuario']);
        $hash_params = $oHash->getArrayCampos();

        echo PostRequest::getContent($url_avisos, $hash_params);
    }
}
