<?php

use frontend\shared\model\ViewNewPhtml;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');

$oPosicion->recordar($Qrefresh);

$Qid_cama = '';
$Qid_habitacion = (string)filter_input(INPUT_POST, 'id_habitacion');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$descripcion = '';
$larga = false;
$vip = false;

if ($Qmod !== 'nuevo') {
    $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!empty($a_sel)) { //vengo de un checkbox
        $Qid_cama = strtok($a_sel[0], "#");
        // el scroll id es de la página anterior, hay que guardarlo allí
        $oPosicion->addParametro('id_sel', $a_sel, 1);
        $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
        $oPosicion->addParametro('scroll_id', $scroll_id, 1);
    } else {
        $Qid_cama = (string)filter_input(INPUT_POST, 'id_cama');
    }

    // Sobre-escribe el scroll_id que se pueda tener
    if (isset($_POST['stack'])) {
        $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    } else {
        $stack = '';
    }

    //Si vengo por medio de Posicion, borro la última
    if ($stack != '') {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new web\Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }

    $CamaRepository = $GLOBALS['container']->get(CamaDlRepositoryInterface::class);
    $oCama = $CamaRepository->findById($Qid_cama);
    if (!empty($oCama)) {
        $Qid_habitacion = $oCama->getIdHabitacion();
        $descripcion = $oCama->getDescripcion() ?? '';
        $larga = $oCama->isLarga() ?? false;
        $vip = $oCama->isVip() ?? false;
    }
}

$oHash = new Hash();
$camposForm = 'descripcion!larga!vip';
$camposChk = 'larga!vip';

$oHash->setCamposForm($camposForm);
$oHash->setCamposChk($camposChk);
$a_camposHidden = array(
    'id_cama' => $Qid_cama,
    'id_habitacion' => $Qid_habitacion,
    'id_ubi' => $Qid_ubi,
    'mod' => $Qmod,
);
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'id_cama' => $Qid_cama,
    'id_habitacion' => $Qid_habitacion,
    'id_ubi' => $Qid_ubi,
    'descripcion' => $descripcion,
    'larga' => $larga,
    'vip' => $vip,
];

$oView = new ViewNewPhtml('frontend\\ubiscamas\\controller');
$oView->renderizar('cama_form.phtml', $a_campos);
