<?php

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use web\ContestarJson;

$Qid_equipaje = (int)filter_input(INPUT_POST, 'id_equipaje');

$error_txt = '';

$EquipajesRepository = $GLOBALS['container']->get(EquipajeRepositoryInterface::class);
$oEquipaje = $EquipajesRepository->findById($Qid_equipaje);

$ids_actividades = $oEquipaje->getIds_activ();
$aId_activ = explode(',', $ids_actividades);
$a = 0;
$a_actividades = [];
$ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
foreach ($aId_activ as $id_activ) {
    $a++;
    $oActividad = $ActividadAllRepository->indById($id_activ);
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