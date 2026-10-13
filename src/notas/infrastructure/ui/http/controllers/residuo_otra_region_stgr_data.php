<?php

use src\notas\application\ResiduoOtraRegionComprobarData;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$idRole = ConfigGlobal::mi_id_role();
if ($idRole !== 1 && $idRole !== 2) {
    ContestarJson::enviar(_('No tiene permiso para ver el residuo de notas.'), []);
    return;
}

$error = '';
$data = [];
try {
    $data = DependencyResolver::get(ResiduoOtraRegionComprobarData::class)->execute();
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
