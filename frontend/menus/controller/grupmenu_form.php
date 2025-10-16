<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Hash;
use frontend\shared\web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qid_grupmenu = (integer)filter_input(INPUT_POST, 'id_grupmenu');

$Qscroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
// Hay que usar isset y empty porque puede tener el valor =0.
// Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new \frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $a_sel = $oPosicion2->getParametro('id_sel');
            if (!empty($a_sel)) {
                $Qid_grupmenu = (integer)strtok($a_sel[0], "#");
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
        $Qid_grupmenu = (integer)strtok($a_sel[0], "#");
        // el scroll id es de la página anterior, hay que guardarlo allí
        $oPosicion->addParametro('id_sel', $a_sel, 1);
        $Qscroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
        $oPosicion->addParametro('scroll_id', $Qscroll_id, 1);
    }
}
$oPosicion->setParametros(array('id_grupmenu' => $Qid_grupmenu), 1);

if (!empty($Qid_grupmenu)) {
    //////////// Nombre de grupo ////////////////////////////////////////////////////////
    $url = '/src/menus/infrastructure/controllers/grupmenu_info.php';
    $parametros = ['id_grupmenu' => $Qid_grupmenu];
    $data = PostRequest::getDataFromUrl($url, $parametros);

    $grupmenu = $data['grupmenu'];
    $orden = $data['orden'];

    $oHashG = new Hash();
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
    $oHashG = new Hash();
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
