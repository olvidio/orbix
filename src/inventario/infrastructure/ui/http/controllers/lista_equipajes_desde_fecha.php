<?php

use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use frontend\shared\web\ContestarJson;

$Qf_ini_iso = (string)filter_input(INPUT_POST, 'f_ini_iso');

$error_txt = '';

$EquipajeRepository = $GLOBALS['container']->get(EquipajeRepositoryInterface::class);
$aOpciones = $EquipajeRepository->getArrayEquipajes($Qf_ini_iso);

$data = [
    'a_opciones' => $aOpciones,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
