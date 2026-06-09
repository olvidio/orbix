<?php
/**
 * Controlador AJAX HTML: modal con el formulario para configurar una
 * condicion sobre una propiedad.
 *
 * Sucesor de la rama `condicion` del dispatcher legacy
 * `apps/cambios/controller/usuario_avisos_pref_ajax.php`. Consume el endpoint
 * JSON `/src/cambios/cambio_usuario_propiedad_pref_item_data` y renderiza
 * `usuario_avisos_pref_condicion.phtml`.
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\DesplegableArray;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$Qobjeto = (string)filter_input(INPUT_POST, 'objeto');
$Qpropiedad = (string)filter_input(INPUT_POST, 'propiedad');
$Qid_item = (int)filter_input(INPUT_POST, 'id_item');

$data = PostRequest::getDataFromUrl('/src/cambios/cambio_usuario_propiedad_pref_item_data', [
    'objeto' => $Qobjeto,
    'propiedad' => $Qpropiedad,
    'id_item' => $Qid_item,
]);
$payload = is_array($data) ? $data : [];

$valor = (string)($payload['valor'] ?? '');
$operador = (string)($payload['operador'] ?? '');
$chk_old = (string)($payload['chk_old'] ?? 'checked');
$chk_new = (string)($payload['chk_new'] ?? 'checked');
$aOpcionesCasas = (array)($payload['aOpcionesCasas'] ?? []);

$a_operadores = [
    '=' => _("igual"),
    '<' => _("menor"),
    '>' => _("mayor"),
    'regexp' => _("regExp"),
];

$oSelects = null;
if ($Qpropiedad === 'id_ubi') {
    $oSelects = new DesplegableArray($valor, $aOpcionesCasas, 'id_ubi');
    $oSelects->setBlanco('t');
    $oSelects->setAccionConjunto('fnjs_mas_casas(event)');
}

$oHash = new HashFront();
$oHash->setCamposForm('salida!objeto!propiedad!operador!valor');
$oHash->setCamposChk('valor_old!valor_new');
$oHash->setCamposNo('id_ubi!id_ubi_mas!id_ubi_num');
$oHash->setArrayCamposHidden(['id_item' => $Qid_item]);

$a_campos = [
    'Qobjeto' => $Qobjeto,
    'Qpropiedad' => $Qpropiedad,
    'Qid_item' => $Qid_item,
    'valor' => $valor,
    'operador' => $operador,
    'chk_old' => $chk_old,
    'chk_new' => $chk_new,
    'a_operadores' => $a_operadores,
    'oSelects' => $oSelects,
    'oHash' => $oHash,
];

$oView = new ViewNewPhtml('frontend\\cambios\\controller');
$oView->renderizar('usuario_avisos_pref_condicion.phtml', $a_campos);
