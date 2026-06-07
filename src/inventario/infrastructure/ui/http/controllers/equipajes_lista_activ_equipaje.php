<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_equipaje = input_int($_POST, 'id_equipaje');

$error_txt = '';

/** @var EquipajeRepositoryInterface $EquipajesRepository */
$EquipajesRepository = DependencyResolver::get(EquipajeRepositoryInterface::class);
$oEquipaje = $EquipajesRepository->findById($Qid_equipaje);
if ($oEquipaje === null) {
    ContestarJson::enviar($error_txt, []);
    return;
}

$ids_actividades = $oEquipaje->getIds_activ() ?? '';
$aId_activ = explode(',', $ids_actividades);
$a = 0;
$a_actividades = [];
/** @var ActividadAllRepositoryInterface $ActividadAllRepository */
$ActividadAllRepository = DependencyResolver::get(ActividadAllRepositoryInterface::class);
foreach ($aId_activ as $id_activ_raw) {
    if (!is_numeric($id_activ_raw)) {
        continue;
    }
    $id_activ = (int) $id_activ_raw;
    $a++;
    $oActividad = $ActividadAllRepository->findById($id_activ);
    if ($oActividad === null) {
        continue;
    }
    $nom_activ = $oActividad->getNom_activ();
    if (empty($nom_activ)) {
        $nom_activ = sprintf(_("OJO! No se encuentra la actividad con id: %s"), $id_activ);
    }
    $a_actividades[$a] = $nom_activ;
}

$data = [
    'a_actividades' => $a_actividades,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);