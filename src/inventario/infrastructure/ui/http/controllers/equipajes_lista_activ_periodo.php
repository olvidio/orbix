<?php

/**
 * Actividades de la casa `id_cdc` en un periodo, para elegir las de un equipaje.
 * La lógica está en {@see \src\inventario\application\EquipajeActividadesData::porPeriodo()}; aquí
 * solo se resuelve el rango de fechas del periodo.
 */

use frontend\shared\web\Periodo;
use src\inventario\application\EquipajeActividadesData;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_cdc = FuncTablasSupport::inputInt($_POST, 'id_cdc');
$Qperiodo = FuncTablasSupport::inputString($_POST, 'periodo');
$Qyear = FuncTablasSupport::inputInt($_POST, 'year');
$Qinicio = FuncTablasSupport::inputString($_POST, 'inicio');
$Qfin = FuncTablasSupport::inputString($_POST, 'fin');

if (empty($Qid_cdc)) {
    ContestarJson::enviar(_("debe seleccionar un lugar"), ['a_valores' => [], 'nombre_ubi' => '']);
    return;
}
if ($Qperiodo === '' || $Qperiodo === 'otro') {
    $inicio = $Qinicio !== '' ? $Qinicio : FuncTablasSupport::inputString($_POST, 'empiezamin');
    $fin = $Qfin !== '' ? $Qfin : FuncTablasSupport::inputString($_POST, 'empiezamax');
    if ($inicio === '' || $fin === '') {
        ContestarJson::enviar(sprintf(_("Faltan parámetros: %s"), 'periodo / empiezamin / empiezamax'), 'none');
        return;
    }
} else {
    $oPeriodo = new Periodo();
    $oPeriodo->setAny(empty($Qyear) ? (int) date('Y') + 1 : $Qyear);
    $oPeriodo->setPeriodo($Qperiodo);
    $inicio = $oPeriodo->getF_ini()->getIso();
    $fin = $oPeriodo->getF_fin()->getIso();
}

/** @var EquipajeActividadesData $useCase */
$useCase = DependencyResolver::get(EquipajeActividadesData::class);
ContestarJson::enviar('', $useCase->porPeriodo($Qid_cdc, $inicio, $fin));
