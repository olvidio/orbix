<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Hash;
use frontend\shared\web\Lista;


// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qfiltro_grupo = (string)filter_input(INPUT_POST, 'filtro_grupo');
$Qnuevo = (string)filter_input(INPUT_POST, 'nuevo');
$Qid_menu = (string)filter_input(INPUT_POST, 'id_menu');

$url_backend = '/src/menus/infrastructure/controllers/lista_grup_menus.php';
$data = PostRequest::getDataFromUrl($url_backend);

$a_valores = $data['a_valores'];

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

$oHashSelect = new Hash();
$oHashSelect->setCamposForm('sel');
$oHashSelect->setcamposNo('scroll_id');
$oHashSelect->setArraycamposHidden(array('que' => 'eliminar_grupmenu'));

$aQuery = ['nuevo' => 1];
$url_nuevo = Hash::link(ConfigGlobal::getWeb()
    . '/frontend/menus/controller/grupmenu_form.php?'
    . http_build_query($aQuery));

$a_campos = [
    'oHashSelect' => $oHashSelect,
    'oTabla' => $oTabla,
    'url_nuevo' => $url_nuevo,
];

$oView = new ViewNewPhtml('frontend\menus\controller');
$oView->renderizar('grupmenu_lista.phtml', $a_campos);