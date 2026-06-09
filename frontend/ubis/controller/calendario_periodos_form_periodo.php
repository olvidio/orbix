<?php

use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_item = (int)filter_input(INPUT_POST, 'id_item');
$data = PostRequest::getDataFromUrl('/src/ubis/calendario_periodos_form_periodo_data', [
    'id_item' => $Qid_item,
]);
$f_ini = $data['f_ini'];
$f_fin = $data['f_fin'];
$sel_sv = $data['sel_sv'];
$sel_sf = $data['sel_sf'];
$sel_res = $data['sel_res'];

$oHash = new HashFront();
$oHash->setArrayCamposHidden([
    'id_item' => $Qid_item,
]);
$oHash->setCamposForm('f_ini!f_fin!sfsv');

$txt = "<form id='frm_periodo'>";
$txt .= $oHash->getCamposHtml();
$txt .= '<h3>' . _('periodo') . '</h3>';
$txt .= _('de') . "<input type=text size=12 name=f_ini value=\"$f_ini\">   " . _('hasta') . " <input type=text size=12 name=f_fin value=\"$f_fin\">";
$txt .= _('asignado a') . " <select name=sfsv><option value=1 $sel_sv>" . _('sv') . "</option>";
$txt .= "<option value=2 $sel_sf>" . _('sf') . "</option>";
$txt .= "<option value=3 $sel_res>" . _('reservado') . "</option></select>";
$txt .= '<br><br>';
$txt .= "<input type='button' value='" . _('guardar') . "' onclick=\"fnjs_guardar(this.form,'guardar');\" >";
$txt .= "<input type='button' value='" . _('eliminar') . "' onclick=\"fnjs_guardar(this.form,'eliminar');\" >";
$txt .= "<input type='button' value='" . _('cancel') . "' onclick=\"fnjs_cerrar();\" >";
$txt .= "</form> ";
echo $txt;
