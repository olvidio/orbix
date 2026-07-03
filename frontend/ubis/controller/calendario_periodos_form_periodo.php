<?php

use frontend\ubis\helpers\UbisPayload;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_item = (int)filter_input(INPUT_POST, 'id_item');
$data = UbisPayload::calendarioPeriodoFields(UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/calendario_periodos_form_periodo_data', [
    'id_item' => $Qid_item,
])));

$oHash = new HashFront();
$oHash->setArrayCamposHidden([
    'id_item' => $Qid_item,
]);
$oHash->setCamposForm('f_ini!f_fin!sfsv');

$txt = "<form id='frm_periodo'>";
$txt .= $oHash->getCamposHtml();
$txt .= '<h3>' . _('periodo') . '</h3>';
$txt .= _('de') . "<input type=text size=12 name=f_ini value=\"{$data['f_ini']}\">   " . _('hasta') . " <input type=text size=12 name=f_fin value=\"{$data['f_fin']}\">";
$txt .= _('asignado a') . " <select name=sfsv><option value=1 {$data['sel_sv']}>" . _('sv') . "</option>";
$txt .= "<option value=2 {$data['sel_sf']}>" . _('sf') . "</option>";
$txt .= "<option value=3 {$data['sel_res']}>" . _('reservado') . "</option></select>";
$txt .= '<br><br>';
$txt .= "<input type='button' value='" . _('guardar') . "' onclick=\"fnjs_guardar(this.form,'guardar');\" >";
$txt .= "<input type='button' value='" . _('eliminar') . "' onclick=\"fnjs_guardar(this.form,'eliminar');\" >";
$txt .= "<input type='button' value='" . _('cancel') . "' onclick=\"fnjs_cerrar();\" >";
$txt .= "</form> ";
echo $txt;
