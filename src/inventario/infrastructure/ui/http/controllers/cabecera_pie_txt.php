<?php

use core\ConfigMagik;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use web\ContestarJson;

$error_txt = '';

$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');

$cabecera = null;
$cabeceraB = null;
$firma = null;
$pie = null;
// Comprobar que no tiene textos propios:
$EquipajeRepository = $GLOBALS['container']->get(EquipajeRepositoryInterface::class);
$oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
if (!empty($oEquipaje)) {
    $cabecera = $oEquipaje->getCabecera();
    $cabeceraB = $oEquipaje->getCabecerab();
    $pie = $oEquipaje->getPie();
}

// create new ConfigMagik-Object
$file = "../cabecera_pie_textos.ini";
$Config = new ConfigMagik($file, true, true);
$Config->SYNCHRONIZE = false;

$cabecera = $cabecera ?? $Config->get("cabecera", "texto_tipo");
$cabeceraB = $cabeceraB ?? $Config->get("cabeceraB", "texto_tipo");
$firma = $firma ?? $Config->get("firma", "texto_tipo");
$pie = $pie ?? $Config->get("pie", "texto_tipo");

$data = [
    'cabecera' => $cabecera,
    'cabeceraB' => $cabeceraB,
    'firma' => $firma,
    'pie' => $pie,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);