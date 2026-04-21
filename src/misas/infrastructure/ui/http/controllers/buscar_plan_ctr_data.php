<?php

use function core\strtoupper_dlb;

use src\misas\application\BuscarPlanCtrData;
use src\shared\domain\value_objects\DateTimeLocal;
use web\ContestarJson;
use web\PeriodoQue;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');

$payload = BuscarPlanCtrData::getData($Qid_zona);
if ($payload['view'] === 'none') {
    ContestarJson::enviar(_('No tiene permiso para ver esta página'));
} else {
    $aOpciones = [
        'esta_semana' => _('esta semana'),
        'este_mes' => _('este mes'),
        'proxima_semana' => _('próxima semana de lunes a domingo'),
        'proximo_mes' => _('próximo mes natural'),
        'separador' => '---------',
        'otro' => _('otro'),
    ];

    $oFormP = new PeriodoQue();
    $oFormP->setFormName('frm_nuevo_periodo');
    $oFormP->setTitulo(strtoupper_dlb(_('seleccionar un periodo')));
    $oFormP->setPosiblesPeriodos($aOpciones);
    $oFormP->setDesplPeriodosOpcion_sel('esta_semana');
    $oFormP->setisDesplAnysVisible(false);

    $ohoy = new DateTimeLocal(date('Y-m-d'));
    $shoy = $ohoy->format('d/m/Y');
    $oFormP->setEmpiezaMin($shoy);
    $oFormP->setEmpiezaMax($shoy);

    $payload['periodo_td_html'] = $oFormP->getTd();

    ContestarJson::enviar('', $payload);
}
