<?php

use src\asistentes\application\ListaUltimQueCtrData;
use frontend\shared\web\ContestarJson;
use frontend\shared\security\HashFront;

$data = ListaUltimQueCtrData::build($_POST);

$hashMain = isset($data['hash_main']) && is_array($data['hash_main']) ? $data['hash_main'] : [];
$oHash = new HashFront();
$oHash->setCamposForm((string)($hashMain['campos_form'] ?? 'id_ubi'));
$cn = (string)($hashMain['campos_no'] ?? '');
if ($cn !== '') {
    $oHash->setCamposNo($cn);
}
$hidden = $hashMain['campos_hidden'] ?? [];
$oHash->setArrayCamposHidden(is_array($hidden) ? $hidden : []);
$data['hash_form_html'] = $oHash->getCamposHtml();

$paths = isset($data['paths']) && is_array($data['paths']) ? $data['paths'] : [];
$data['form_action'] = (string)($paths['form_action'] ?? '');
unset($data['hash_main'], $data['paths']);

ContestarJson::enviar('', $data);
