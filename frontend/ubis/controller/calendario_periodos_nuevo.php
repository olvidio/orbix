<?php

use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$data = PostRequest::getDataFromUrl('/src/ubis/calendario_periodos_nuevo_data', [
    'id_ubi' => $Qid_ubi,
    'year' => $Qyear,
]);
$f_next = $data['f_next'];
$sf_chk = $data['sf_chk'];
$sv_chk = $data['sv_chk'];

$oHash = new HashFront();
$oHash->setArrayCamposHidden([
    'id_ubi' => $Qid_ubi,
]);
$oHash->setCamposForm('f_ini!f_fin!sfsv');

$txt = "<form id='frm_periodo'>";
$txt .= $oHash->getCamposHtml();
$txt .= '<h3>' . _('periodo') . '</h3>';
$txt .= _('de') . "<input type=text size=12 name=f_ini value=\"$f_next\">   " . _('hasta') . " <input type=text size=12 name=f_fin value=\"\">";
$txt .= _('asignado a') . " <select name=sfsv>";
$txt .= "<option value=1 $sv_chk>" . _('sv') . "</option>";
$txt .= "<option value=2 $sf_chk>" . _('sf') . "</option>";
$txt .= "<option value=3 >" . _('reservado') . "</option></select>";
$txt .= '<br><br>';
$txt .= "<input type='button' value='" . _('guardar') . "' onclick=\"fnjs_guardar(this.form,'guardar');\" >";
$txt .= "<input type='button' value='" . _('cancel') . "' onclick=\"fnjs_cerrar();\" >";
$txt .= "</form> ";
echo $txt;
