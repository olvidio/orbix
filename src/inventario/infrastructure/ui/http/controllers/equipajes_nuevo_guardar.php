<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\entity\Equipaje;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\web\ContestarJson;

$Qid_ubi_activ = input_int($_POST, 'id_ubi_activ');
$Qnom_equipaje = input_string($_POST, 'nom_equipaje');
$Qids_activ = input_string($_POST, 'ids_activ');
$Qf_ini = input_string($_POST, 'f_ini');
if (empty($Qf_ini)) {
    $oF_ini = null;
} else {
    $rawF_ini = DateTimeLocal::createFromLocal($Qf_ini);
    $oF_ini = $rawF_ini instanceof DateTimeLocal ? $rawF_ini : null;
}
$Qf_fin = input_string($_POST, 'f_fin');
if (empty($Qf_fin)) {
    $oF_fin = null;
} else {
    $rawF_fin = DateTimeLocal::createFromLocal($Qf_fin);
    $oF_fin = $rawF_fin instanceof DateTimeLocal ? $rawF_fin : null;
}
$Qlugar = input_string($_POST, 'lugar');

$error_txt = '';

/** @var EquipajeRepositoryInterface $EquipajesRepository */
$EquipajesRepository = DependencyResolver::get(EquipajeRepositoryInterface::class);
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

