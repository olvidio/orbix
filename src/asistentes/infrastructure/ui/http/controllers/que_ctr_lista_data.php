<?php

use src\asistentes\application\QueCtrListaData;
use frontend\shared\web\ContestarJson;
use frontend\shared\web\PeriodoQue;
use frontend\shared\security\HashFront;

$data = QueCtrListaData::build($_POST);

$hashMain = isset($data['hash_main']) && is_array($data['hash_main']) ? $data['hash_main'] : [];
$oHash = new HashFront();
$oHash->setCamposForm((string)($hashMain['campos_form'] ?? ''));
$cn = (string)($hashMain['campos_no'] ?? '');
if ($cn !== '') {
    $oHash->setCamposNo($cn);
}
$hidden = $hashMain['campos_hidden'] ?? [];
$oHash->setArrayCamposHidden(is_array($hidden) ? $hidden : []);
$data['hash_form_html'] = $oHash->getCamposHtml();
unset($data['hash_main']);

$pf = $data['periodo_form'] ?? null;
if (is_array($pf)) {
    $oFormP = new PeriodoQue();
    $oFormP->setFormName((string)($pf['form_name'] ?? 'modifica'));
    $oFormP->setTitulo((string)($pf['titulo'] ?? ''));
    $oFormP->setPosiblesPeriodos((array)($pf['opciones_periodos'] ?? []));
    $oFormP->setDesplPeriodosOpcion_sel($pf['periodo_sel'] ?? 'tot_any');
    $oFormP->setDesplAnysOpcion_sel($pf['year_sel'] ?? (int)date('Y'));
    $data['periodo_form_html'] = $oFormP->getHtml();
} else {
    $data['periodo_form_html'] = '';
}
unset($data['periodo_form']);

ContestarJson::enviar('', $data);
