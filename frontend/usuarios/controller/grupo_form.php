<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');

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

if (!empty($Qid_usuario)) {
    //////////// Nombre de grupo ////////////////////////////////////////////////////////
    $url_backend = '/src/usuarios/infrastructure/controllers/grupo_info.php';
    $a_campos = [ 'id_usuario' => $Qid_usuario];
    $data = PostRequest::getDataFromUrl($url_backend, $a_campos);

    $usuario = $data['nombre'];

    $oHashG = new Hash();
    $oHashG->setCamposForm('que!usuario');
    $oHashG->setcamposNo('id_ctr!id_sacd!casas!refresh');
    $a_camposHidden = array(
        'id_usuario' => $Qid_usuario,
    );
    $oHashG->setArraycamposHidden($a_camposHidden);

    $txt_guardar = _("guardar datos grupo");
    $a_camposG = [
        'oPosicion' => $oPosicion,
        'oHashG' => $oHashG,
        'usuario' => $usuario,
        'txt_guardar' => $txt_guardar,
    ];

    $oView = new ViewNewPhtml('frontend\usuarios\controller');
    $oView->renderizar('grupo_form.phtml', $a_camposG);

    //////////// Permisos de grupos //////////////////////////////////////////////////
    $url_backend = '/src/usuarios/infrastructure/controllers/perm_menu_lista.php';
    $a_campos = [ 'id_usuario' => $Qid_usuario];
    $data = PostRequest::getDataFromUrl($url_backend, $a_campos);

    $a_cabeceras = $data['a_cabeceras'];
    $a_botones = $data['a_botones'];
    $a_valores = $data['a_valores'];

    $oTablaPermMenu = new Lista();
    $oTablaPermMenu->setId_tabla('form_perm_menu');
    $oTablaPermMenu->setCabeceras($a_cabeceras);
    $oTablaPermMenu->setBotones($a_botones);
    $oTablaPermMenu->setDatos($a_valores);

    $oHashPermisos = new Hash();
    $oHashPermisos->setCamposForm('que!sel');
    $oHashPermisos->setcamposNo('scroll_id!refresh');
    $a_camposHidden = array(
        'id_usuario' => $Qid_usuario,
    );
    $oHashPermisos->setArraycamposHidden($a_camposHidden);

    if (!empty($Qid_usuario)) { // si no hay usuario, no puedo poner permisos.
        // Permisos
        $a_camposP = [
            'oHashPermisos' => $oHashPermisos,
            'oTablaPermMenu' => $oTablaPermMenu,
        ];

        $oView = new ViewNewPhtml('frontend\usuarios\controller');
        $oView->renderizar('perm_menu_lista.phtml', $a_camposP);
    }

    //////////// Permisos en actividades ////////////////////////////////////////////////
    if (ConfigGlobal::is_app_installed('procesos')) {
        $url = Hash::cmdSinParametros(ConfigGlobal::getWeb()
            . '/frontend/usuarios/controller/perm_activ_lista.php'
        );

        $oHash = new Hash();
        $oHash->setUrl($url);
        $oHash->setArrayCamposHidden(['id_usuario' => $Qid_usuario]);
        $hash_params = $oHash->getArrayCampos();

        echo PostRequest::getContent($url, $hash_params);
    }
} else {
    $oHashG = new Hash();
    $oHashG->setCamposForm('que!usuario');
    $oHashG->setcamposNo('id_ctr!id_sacd!casas!refresh');
    $a_camposHidden = array(
        'id_usuario' => '',
    );
    $oHashG->setArraycamposHidden($a_camposHidden);

    $txt_guardar = _("guardar datos grupo");
    $a_camposG = [
        'oPosicion' => $oPosicion,
        'oHashG' => $oHashG,
        'usuario' => '',
        'txt_guardar' => $txt_guardar,
    ];

    $oView = new ViewNewPhtml('frontend\usuarios\controller');
    $oView->renderizar('grupo_form.phtml', $a_camposG);
}
