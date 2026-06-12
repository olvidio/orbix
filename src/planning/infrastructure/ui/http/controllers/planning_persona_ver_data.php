<?php

use frontend\shared\web\Periodo;
use src\planning\application\PlanningPersonaVerData;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_string_list;

$error = '';
$data = [];
try {
    $input = $_POST;
    $sSeleccionados = input_string($input, 'sSeleccionados');
    if ($sSeleccionados !== '') {
        $aid_nom = array_values(array_filter(
            array_map('trim', explode(',', $sSeleccionados)),
            static fn (string $v): bool => $v !== ''
        ));
    } else {
        $aid_nom = input_string_list($input, 'sel');
    }

    $Qyear = input_int($input, 'year');
    $Qperiodo = input_string($input, 'periodo');
    $Qempiezamin = input_string($input, 'empiezamin');
    $Qempiezamax = input_string($input, 'empiezamax');

    $oPeriodo = Periodo::conCalendarioDesdeBackend();
    $oPeriodo->setDefaultAny('next');
    $oPeriodo->setAny($Qyear);
    $oPeriodo->setEmpiezaMin($Qempiezamin);
    $oPeriodo->setEmpiezaMax($Qempiezamax);
    $oPeriodo->setPeriodo($Qperiodo);

    $inicio_iso = $oPeriodo->getF_ini_iso() ?? '';
    $fin_iso = $oPeriodo->getF_fin_iso() ?? '';
    if ($inicio_iso === '' || $fin_iso === '') {
        throw new \RuntimeException(_('Faltan fechas de periodo'));
    }
    $oIniPlanning = new DateTimeLocal($inicio_iso);
    $inicio_local = $oIniPlanning->getFromLocal();

    /** @var PlanningPersonaVerData $useCase */
    $useCase = DependencyResolver::get(PlanningPersonaVerData::class);
    $data = $useCase->execute($input, $aid_nom, $oIniPlanning, $inicio_local, $fin_iso, $inicio_iso);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
