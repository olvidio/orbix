<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\menus\helpers\MenusPayload;
use frontend\shared\helpers\ListNavSupport;


// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

/** @var string|list<string> $Qid_sel */
$Qid_sel = ListNavSupport::idSelFromPost();
$Qscroll_id = ListNavSupport::scrollIdFromPost();

$navState = ListNavSupport::mergeSelectionIntoReturnParametros([], $Qid_sel, $Qscroll_id);
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, []);

$url_backend = '/src/menus/grupmenu_lista';
$data = PostRequest::getDataFromUrl($url_backend);

$a_valores = MenusPayload::listaDatos($data['a_valores'] ?? []);

$a_cabeceras = [_("grupMenu"),
    _("orden"),
];

$a_botones[] = ['txt' => _("modificar"), 'click' => "fnjs_modificar(this.form)"];
$a_botones[] = ['txt' => _("borrar"), 'click' => "fnjs_eliminar(this.form)"];


if (isset($Qid_sel) && !empty($Qid_sel)) {
    $a_valores['select'] = $Qid_sel;
}
if (isset($Qscroll_id) && !empty($Qscroll_id)) {
    $a_valores['scroll_id'] = $Qscroll_id;
}

$oTabla = new Lista();
$oTabla->setId_tabla('grupmenu_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$oHashSelect = new HashFront();
$oHashSelect->setCamposForm('sel');
$oHashSelect->setcamposNo('scroll_id');
$oHashSelect->setArraycamposHidden(array('que' => 'eliminar_grupmenu'));

$aQuery = ['nuevo' => 1];
$url_nuevo = HashFront::link(AppUrlConfig::getPublicAppBaseUrl()
    . '/frontend/menus/controller/grupmenu_form.php?'
    . http_build_query($aQuery));

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashSelect' => $oHashSelect,
    'oTabla' => $oTabla,
    'url_nuevo' => $url_nuevo,
];

$oView = new ViewNewPhtml('frontend\menus\controller');
$oView->renderizar('grupmenu_lista.phtml', $a_campos);