<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FuncTablasSupport;

use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_equipaje = FuncTablasSupport::inputInt($_POST, 'id_equipaje');

$error_txt = '';

// generar maletas

// posibles maletas: dlb-Magatzem-->id_ubi=40
$id_ubi = 40;
// quitar las ya asignadas
// equipajes coincidentes
/** @var EquipajeRepositoryInterface $EquipajeRepository */
$EquipajeRepository = DependencyResolver::get(EquipajeRepositoryInterface::class);
$oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
if ($oEquipaje === null) {
    ContestarJson::enviar($error_txt, []);
    return;
}
$fIni = $oEquipaje->getF_ini();
$fFin = $oEquipaje->getF_fin();
$f_ini_iso = $fIni !== null ? $fIni->getIso() : '';
$f_fin_iso = $fFin !== null ? $fFin->getIso() : '';
$aEquipajes = $EquipajeRepository->getEquipajesCoincidentes($f_ini_iso, $f_fin_iso);
/** @var EgmRepositoryInterface $EgmRepository */
$EgmRepository = DependencyResolver::get(EgmRepositoryInterface::class);
$aEgms = $EgmRepository->getArrayIdFromIdEquipajes($aEquipajes, 'lugar');

// último id_grupo:
$new_id_grupo = $EgmRepository->getUltimoGrupo($Qid_equipaje) + 1;

/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);
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
