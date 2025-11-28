<?php

use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use web\ContestarJson;

$Qid_equipaje = (string)filter_input(INPUT_POST, 'id_equipaje');

$error_txt = '';

// generar maletas

// posibles maletas: dlb-Magatzem-->id_ubi=40
$id_ubi = 40;
// quitar las ya asignadas
// equipajes coincidentes
$EquipajeRepository = $GLOBALS['container']->get(EquipajeRepositoryInterface::class);
$oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
$f_ini_iso = $oEquipaje->getF_ini()->getIso();
$f_fin_iso = $oEquipaje->getF_fin()->getIso();
$aEquipajes = $EquipajeRepository->getEquipajesCoincidentes($f_ini_iso, $f_fin_iso);
$EgmRepository = $GLOBALS['container']->get(EgmRepositoryInterface::class);
$aEgms = $EgmRepository->getArrayIdFromIdEquipajes($aEquipajes, 'lugar');

// último id_grupo:
$new_id_grupo = $EgmRepository->getUltimoGrupo($Qid_equipaje) + 1;

$LugarRepository = $GLOBALS['container']->get(LugarRepositoryInterface::class);
$cLugares = $LugarRepository->getLugares(['id_ubi' => $id_ubi]);
$aOpciones = [];
foreach ($cLugares as $oLugar) {
    $id_lugar = $oLugar->getId_lugar();
    if (!in_array($id_lugar, $aEgms)) {
        $aOpciones[$id_lugar] = $oLugar->getNom_lugar();
    }
}
$aOpciones[1] = _("nuevo");
asort($aOpciones);

$data = [
    'a_opciones' => $aOpciones,
    'new_id_grupo' => $new_id_grupo,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
