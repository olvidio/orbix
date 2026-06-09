<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use function frontend\shared\helpers\is_true;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_ubi = (int)(filter_input(INPUT_POST, 'id_ubi') ?? filter_input(INPUT_GET, 'id_ubi'));
$form = ubis_centro_plazas_form_from_payload(ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/centros_form_plazas', ['id_ubi' => $Qid_ubi])));

$chk_sede = is_true($form['sede']) ? 'checked' : '';

$url_update = AppUrlConfig::getApiBaseUrl() . '/src/ubis/centros_update';

$oHash = new HashFront();
$oHash->setUrl($url_update);
$oHash->setArrayCamposHidden([
    'id_ubi' => $Qid_ubi,
]);
$oHash->setCamposForm('num_habit_indiv!plazas');
$oHash->setCamposChk('sede');

$txt = "<form id='frm_plazas' action='$url_update'>";
$txt .= $oHash->getCamposHtml();
$txt .= '<h3>' . _("centro") . '  ' . $form['nombre_ubi'] . '</h3>';
$txt .= _("número de habitaciones individuales") . '   <input type=text size=12 name=num_habit_indiv value="' . $form['num_habit_indiv'] . '">';
$txt .= '<br>';
$txt .= _("plazas") . '   <input type=text size=12 name=plazas value="' . $form['plazas'] . '">';
$txt .= '<br>';
$txt .= "<input type=hidden name=sede value=\"false\">"; // para evitar valor null.
$txt .= _("sede") . "   <input type=checkbox size=12 name=sede $chk_sede value=\"true\">";
$txt .= '<br><br>';
$txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar('#frm_plazas','guardar');\" >";
$txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
$txt .= "</form> ";
echo $txt;
