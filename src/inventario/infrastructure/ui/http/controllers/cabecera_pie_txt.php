<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

use src\shared\config\ConfigGlobal;
use src\shared\config\ConfigMagik;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\shared\web\ContestarJson;

$error_txt = '';

$Qid_equipaje = input_int($_POST, 'id_equipaje');

$cabecera = null;
$cabeceraB = null;
$pie = null;
// Comprobar que no tiene textos propios:
/** @var EquipajeRepositoryInterface $EquipajeRepository */
$EquipajeRepository = DependencyResolver::get(EquipajeRepositoryInterface::class);
$oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
if (!empty($oEquipaje)) {
    $cabecera = $oEquipaje->getCabecera();
    $cabeceraB = $oEquipaje->getCabecerab();
    $pie = $oEquipaje->getPie();
}

// create new ConfigMagik-Object
$file = ConfigGlobal::$dir_web ."/data/inventario/cabecera_pie_textos.ini";
$Config = new ConfigMagik($file, true, true);
$Config->SYNCHRONIZE = false;

$cabecera = $cabecera ?? $Config->get("cabecera", "texto_tipo");
$cabeceraB = $cabeceraB ?? $Config->get("cabeceraB", "texto_tipo");
$firma = $Config->get("firma", "texto_tipo");
$pie = $pie ?? $Config->get("pie", "texto_tipo");

$data = [
    'cabecera' => $cabecera,
    'cabeceraB' => $cabeceraB,
    'firma' => $firma,
    'pie' => $pie,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);