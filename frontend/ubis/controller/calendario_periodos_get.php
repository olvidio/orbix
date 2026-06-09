<?php

use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qque = 'get';
$data = ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/calendario_periodos_get_data', [
    'id_ubi' => $Qid_ubi,
]));
$rows = ubis_calendario_periodo_rows($data['rows'] ?? []);
$oHash = new HashFront();
$i = 0;
$txt = '';
foreach ($rows as $row) {
    $i++;
    $id_item = $row['id_item'];
    $id_ubi = $row['id_ubi'];
    $f_ini = $row['f_ini'];
    $f_fin = $row['f_fin'];
    $sel_sv = $row['sel_sv'];
    $sel_sf = $row['sel_sf'];
    $sel_res = $row['sel_res'];

    $form = $i . '_form_' . $id_ubi;
    $form_id_ubi = $i . '_form_' . $id_ubi . '_id_ubi';
    $form_que = $i . '_form_' . $id_ubi . '_que';

    $a_camposHidden = [
        'id_item' => $id_item,
    ];
    $camposForm = 'f_ini!f_fin!sfsv';
    $camposNo = 'que!id_ubi';
    $oHash->setCamposNo($camposNo);
    $oHash->setCamposForm($camposForm);
    $oHash->setArrayCamposHidden($a_camposHidden);

    $txt .= "<form id=\"$form\"> ";
    $txt .= $oHash->getCamposHtml();
    $txt .= "<input type=hidden id=\"$form_id_ubi\" name=\"id_ubi\" value=\"\" > ";
    $txt .= "<input type=hidden id=\"$form_que\" name=\"que\" value=\"\" > ";
    $txt .= "<input type=hidden name=id_item value=\"$id_item\" > ";
    $txt .= _('de') . "<input type=text size=12 name=f_ini value=\"$f_ini\">   " . _('hasta') . " <input type=text size=12 name=f_fin value=\"$f_fin\">";
    $txt .= _('asignado a') . " <select name=sfsv><option value=1 $sel_sv>" . _('sv') . "</option>";
    $txt .= "<option value=2 $sel_sf>" . _('sf') . "</option>";
    $txt .= "<option value=3 $sel_res>" . _('reservado') . "</option></select>";
    $txt .= "  <span class=link onclick=fnjs_grabar($id_ubi,$i,'update')>" . _('grabar') . "</span>";
    $txt .= "  <span class=link onclick=fnjs_grabar($id_ubi,$i,'borrar')>" . _('borrar') . "</span></form>";
}
ubis_json_echo(['que' => $Qque, 'txt' => $txt]);
