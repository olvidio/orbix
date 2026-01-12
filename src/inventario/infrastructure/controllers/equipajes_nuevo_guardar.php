<?php

use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\entity\Equipaje;
use web\ContestarJson;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

$Qid_ubi_activ = (integer)filter_input(INPUT_POST, 'id_ubi_activ');
$Qnom_equipaje = (string)filter_input(INPUT_POST, 'nom_equipaje');
$Qids_activ = (string)filter_input(INPUT_POST, 'ids_activ');
$Qf_ini = (string)filter_input(INPUT_POST, 'f_ini');
if (empty($Qf_ini)) {
    $oF_ini = new NullDateTimeLocal();
} else {
    $oF_ini = DateTimeLocal::createFromLocal($Qf_ini);
}
$Qf_fin = (string)filter_input(INPUT_POST, 'f_fin');
if (empty($Qf_fin)) {
    $oF_fin = new NullDateTimeLocal();
} else {
    $oF_fin = DateTimeLocal::createFromLocal($Qf_fin);
}
$Qlugar = (string)filter_input(INPUT_POST, 'lugar');

$error_txt = '';

$EquipajesRepository = $GLOBALS['container']->get(EquipajeRepositoryInterface::class);
$newId = $EquipajesRepository->getNewId();

$oEquipaje = new Equipaje();
$oEquipaje->setId_equipaje($newId);
$oEquipaje->setIds_activ($Qids_activ);
$oEquipaje->setLugarVo($Qlugar);
$oEquipaje->setF_ini($oF_ini);
$oEquipaje->setF_fin($oF_fin);
$oEquipaje->setId_ubi_activ($Qid_ubi_activ);
$oEquipaje->setNom_equipaje($Qnom_equipaje);

if ($EquipajesRepository->Guardar($oEquipaje) === false) {
    $error_txt .= _("hay un error, no se ha guardado");
    $error_txt .= "\n" . $EquipajesRepository->getErrorTxt();
}

ContestarJson::enviar($error_txt, 'ok');

