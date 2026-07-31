<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\menus\helpers\MenusPostInput;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\PayloadCoercion;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)filter_input(INPUT_POST, 'refresh');

$Qid_grupmenu = (integer)filter_input(INPUT_POST, 'id_grupmenu');

$restored = ListNavSupport::restoreSelectionFromStackPost();
$Qscroll_id = $restored['scroll_id'] !== '' ? $restored['scroll_id'] : ListNavSupport::scrollIdFromPost();
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!ListNavSupport::idSelIsEmpty($restored['id_sel'])) {
    $a_sel = is_array($restored['id_sel']) ? $restored['id_sel'] : [$restored['id_sel']];
}
if (!empty($a_sel)) {
    $Qque = (string)filter_input(INPUT_POST, 'que');
    if ($Qque !== 'del_grupmenu') {
        $Qid_grupmenu = MenusPostInput::idFromSelItem(MenusPostInput::selFirstItem($a_sel));
    }
}

$navIdentity = $Qid_grupmenu > 0 ? ['id_grupmenu' => $Qid_grupmenu] : [];
$navState = ListNavSupport::mergeSelectionForRecordar(
    ListNavSupport::buildReturnParametrosFromPost(),
    ListNavSupport::idSelFromPost(),
    $Qscroll_id,
);
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $navIdentity,
    $navState,
);
ListNavSupport::syncNavStateAt(
    $oPosicion,
    1,
    ListNavSupport::buildSelectionStatePatchFromPost(),
);

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
