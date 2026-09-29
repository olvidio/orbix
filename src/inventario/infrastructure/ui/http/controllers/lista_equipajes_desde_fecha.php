<?php

use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\shared\security\HashB;
use src\shared\web\ContestarJson;

$Qf_ini_iso = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_ini_iso');

$error_txt = '';

/** @var EquipajeRepositoryInterface $EquipajeRepository */
$EquipajeRepository = DependencyResolver::get(EquipajeRepositoryInterface::class);
$aOpciones = $EquipajeRepository->getArrayEquipajes($Qf_ini_iso);

$equipaje_ctx_map = [];
$equipaje_ctx_texto_map = [];
foreach (array_keys($aOpciones) as $id_equipaje) {
    $equipaje_ctx_map[(string) $id_equipaje] = HashB::sign('equipajes_eliminar', ['id_equipaje' => (int) $id_equipaje]);
    $equipaje_ctx_texto_map[(string) $id_equipaje] = HashB::sign('equipajes_texto_listado_guardar', ['id_equipaje' => (int) $id_equipaje]);
}

$data = [
    'a_opciones' => $aOpciones,
    'equipaje_ctx_map' => $equipaje_ctx_map,
    'equipaje_ctx_texto_map' => $equipaje_ctx_texto_map,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
