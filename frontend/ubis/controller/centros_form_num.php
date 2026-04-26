<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$Qid_ubi = (int)(filter_input(INPUT_POST, 'id_ubi') ?? filter_input(INPUT_GET, 'id_ubi'));
$data = PostRequest::getDataFromUrl('/src/ubis/centros_form_num', ['id_ubi' => $Qid_ubi]);

$nombre_ubi = $data['nombre_ubi'] ?? '';
$n_buzon = $data['n_buzon'] ?? '';
$num_pi = $data['num_pi'] ?? '';
$num_cartas = $data['num_cartas'] ?? '';

$url_update = AppUrlConfig::getApiBaseUrl() . '/src/ubis/centros_update';

$oHash = new Hash();
$oHash->setUrl($url_update);
$oHash->setArrayCamposHidden([
    'id_ubi' => $Qid_ubi,
]);
$oHash->setCamposForm('n_buzon!num_pi!num_cartas');

$txt = "<form id='frm_num' action='$url_update'>";
$txt .= $oHash->getCamposHtml();
$txt .= '<h3>' . _("centro") . '  ' . $nombre_ubi . '</h3>';
$txt .= _("número de buzón") . "   <input type=text size=12 name=n_buzon value=\"$n_buzon\">";
$txt .= '<br>';
$txt .= _("número de pi") . "   <input type=text size=12 name=num_pi value=\"$num_pi\">";
$txt .= '<br>';
$txt .= _("número de cartas") . "   <input type=text size=12 name=num_cartas value=\"$num_cartas\">";
$txt .= '<br><br>';
$txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar('#frm_num');\" >";
$txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
$txt .= "</form> ";
echo $txt;

