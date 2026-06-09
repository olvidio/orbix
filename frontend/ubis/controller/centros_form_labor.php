<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\permisos\MenuPermisoMenuHtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qid_ubi = (int)(filter_input(INPUT_POST, 'id_ubi') ?? filter_input(INPUT_GET, 'id_ubi'));
$form = ubis_centro_labor_form_from_payload(ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/centros_form_labor', ['id_ubi' => $Qid_ubi])));

$tipo_labor_check_html = MenuPermisoMenuHtml::cuadrosCheck('tipo_labor', $form['tipo_labor'], $form['tipo_labor_bit_map']);

$url_update = AppUrlConfig::getApiBaseUrl() . '/src/ubis/centros_update';

$oHash = new HashFront();
$oHash->setUrl($url_update);
$oHash->setArrayCamposHidden([
    'labor' => 'si',
    'id_ubi' => $Qid_ubi,
]);
$oHash->setCamposForm('tipo_ctr!tipo_labor');

$txt = "<form id='frm_labor' action='$url_update'>";
$txt .= $oHash->getCamposHtml();
$txt .= '<h3>' . _("centro") . '  ' . $form['nombre_ubi'] . '</h3>';
$txt .= _("tipo de centro") . "   <input type=text size=12 name=tipo_ctr value=\"{$form['tipo_ctr']}\">";
$txt .= '<br>';
$txt .= _("tipo de labor");
$txt .= '   ';
$txt .= $tipo_labor_check_html;
$txt .= '<br><br>';
$txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar('#frm_labor');\" >";
$txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
$txt .= "</form> ";
echo $txt;
