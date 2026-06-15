<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once __DIR__ . '/../helpers/menus_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');

$Qid_grupmenu = (integer)filter_input(INPUT_POST, 'id_grupmenu');

$Qscroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
// Hay que usar isset y empty porque puede tener el valor =0.
// Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new \frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $a_sel = $oPosicion2->getParametro('id_sel');
            if (!empty($a_sel)) {
                $Qid_grupmenu = menus_id_from_sel_item(menus_sel_first_item($a_sel));
            } else {
                $Qid_grupmenu = $oPosicion2->getParametro('id_grupmenu');
            }
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
} elseif (!empty($a_sel)) { //vengo de un checkbox
    $Qque = (string)filter_input(INPUT_POST, 'que');
    if ($Qque !== 'del_grupmenu') { //En el caso de venir de borrar un grupmenu, no hago nada
        $Qid_grupmenu = menus_id_from_sel_item(menus_sel_first_item($a_sel));
    }
}
list_nav_boot_recordar($oPosicion, $Qrefresh);
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros(list_nav_build_return_parametros_from_post(), list_nav_id_sel_from_post(), isset($Qscroll_id) ? (string) $Qscroll_id : ''));

$oPosicion->setParametros(array('id_grupmenu' => $Qid_grupmenu), 1);

if (!empty($Qid_grupmenu)) {
    //////////// Nombre de grupo ////////////////////////////////////////////////////////
    $url = '/src/menus/grupmenu_info';
    $parametros = ['id_grupmenu' => $Qid_grupmenu];
    $data = PostRequest::getDataFromUrl($url, $parametros);

    $grupmenu = $data['grupmenu'];
    $orden = $data['orden'];

    $oHashG = new HashFront();
    $oHashG->setCamposForm('que!grupmenu!orden');
    $oHashG->setcamposNo('refresh');
    $a_camposHidden = array(
        'id_grupmenu' => $Qid_grupmenu,
    );
    $oHashG->setArraycamposHidden($a_camposHidden);

    $txt_guardar = _("guardar grupmenu");
    $a_camposG = [
        'oPosicion' => $oPosicion,
        'oHashG' => $oHashG,
        'grupmenu' => $grupmenu,
        'orden' => $orden,
        'txt_guardar' => $txt_guardar,
    ];

    $oView = new ViewNewPhtml('frontend\menus\controller');
    $oView->renderizar('grupmenu_form.phtml', $a_camposG);

} else {
    $oHashG = new HashFront();
    $oHashG->setCamposForm('que!grupmenu!orden');
    $oHashG->setcamposNo('refresh');
    $a_camposHidden = array(
        'id_grupmenu' => $Qid_grupmenu,
    );
    $oHashG->setArraycamposHidden($a_camposHidden);

    $txt_guardar = _("guardar grupmenu");
    $a_camposG = [
        'oPosicion' => $oPosicion,
        'oHashG' => $oHashG,
        'grupmenu' => '',
        'orden' => '',
        'txt_guardar' => $txt_guardar,
    ];

    $oView = new ViewNewPhtml('frontend\menus\controller');
    $oView->renderizar('grupmenu_form.phtml', $a_camposG);
}
