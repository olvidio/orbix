<?php

use src\shared\config\ConfigGlobal;
use frontend\shared\PostRequest;
use src\ubis\domain\CuadrosLabor;
use web\Hash;

require_once("frontend/shared/global_header_front.inc");

$Qid_ubi = (int)(filter_input(INPUT_POST, 'id_ubi') ?? filter_input(INPUT_GET, 'id_ubi'));
$data = PostRequest::getDataFromUrl('/src/ubis/centros_form_labor', ['id_ubi' => $Qid_ubi]);
$oPermActiv = new CuadrosLabor();

$nombre_ubi = $data['nombre_ubi'] ?? '';
$tipo_ctr = $data['tipo_ctr'] ?? '';
$tipo_labor = (int)($data['tipo_labor'] ?? 0);

$url_update = rtrim(ConfigGlobal::getWeb(), '/') . '/src/ubis/centros_update';

$oHash = new Hash();
$oHash->setUrl($url_update);
$oHash->setArrayCamposHidden([
    'labor' => 'si', // para saber que si el array tipo_labor está en blanco hay que borrar.
    'id_ubi' => $Qid_ubi,
]);
$oHash->setCamposForm('tipo_ctr!tipo_labor');

$txt = "<form id='frm_labor' action='$url_update'>";
$txt .= $oHash->getCamposHtml();
$txt .= '<h3>' . _("centro") . '  ' . $nombre_ubi . '</h3>';
$txt .= _("tipo de centro") . "   <input type=text size=12 name=tipo_ctr value=\"$tipo_ctr\">";
$txt .= '<br>';
$txt .= _("tipo de labor");
$txt .= '   ';
$txt .= $oPermActiv->cuadros_check('tipo_labor', $tipo_labor);
$txt .= '<br><br>';
$txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar('#frm_labor');\" >";
$txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
$txt .= "</form> ";
echo $txt;

