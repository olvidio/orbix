<?php

use function core\strtoupper_dlb;

use src\misas\application\PlanDeMisasPantallaData;
use src\shared\domain\value_objects\DateTimeLocal;
use web\ContestarJson;
use web\PeriodoQue;

$pantalla = (string)filter_input(INPUT_POST, 'pantalla');
if ($pantalla === '') {
    $pantalla = 'preparar';
}

$base = PlanDeMisasPantallaData::getData($pantalla);

if ($pantalla === 'preparar') {
    $aOpciones = [
        'proxima_semana' => _('próxima semana de lunes a domingo'),
        'proximo_mes' => _('próximo mes natural'),
        'otro' => _('otro'),
    ];
    $sel = 'proxima_semana';
} elseif ($pantalla === 'modificar') {
    $aOpciones = [
        'esta_semana' => _('esta semana'),
        'este_mes' => _('este mes'),
        'proxima_semana' => _('próxima semana de lunes a domingo'),
        'proximo_mes' => _('próximo mes natural'),
        'separador' => '---------',
        'otro' => _('otro'),
    ];
    $sel = 'proximo_mes';
} else {
    $aOpciones = [
        'esta_semana' => _('esta semana'),
        'este_mes' => _('este mes'),
        'proxima_semana' => _('próxima semana de lunes a domingo'),
        'proximo_mes' => _('próximo mes natural'),
        'separador' => '---------',
        'otro' => _('otro'),
    ];
    $sel = 'este_mes';
}

$oFormP = new PeriodoQue();
$oFormP->setFormName('frm_nuevo_periodo');
$oFormP->setTitulo(strtoupper_dlb(_('seleccionar un periodo')));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplPeriodosOpcion_sel($sel);
$oFormP->setisDesplAnysVisible(false);

$ohoy = new DateTimeLocal(date('Y-m-d'));
$shoy = $ohoy->format('d/m/Y');
$oFormP->setEmpiezaMin($shoy);
$oFormP->setEmpiezaMax($shoy);

$base['periodo_td_html'] = $oFormP->getTd();

ContestarJson::enviar('', $base);
