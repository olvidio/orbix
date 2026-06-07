<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\shared\web\ContestarJson;

$Qf_ini_iso = input_string($_POST, 'f_ini_iso');

$error_txt = '';

/** @var EquipajeRepositoryInterface $EquipajeRepository */
$EquipajeRepository = DependencyResolver::get(EquipajeRepositoryInterface::class);
$aOpciones = $EquipajeRepository->getArrayEquipajes($Qf_ini_iso);

$data = [
    'a_opciones' => $aOpciones,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
