<?php

use src\actividadestudios\application\MatriculasListaOtrasRData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubis\domain\RegionStgrAviso;
use function src\shared\domain\helpers\input_string;

$error = '';
$data = [];
try {
    $apellido1 = input_string($_POST, 'apellido1');
    $esquema = input_string($_POST, 'esquema');
    /** @var MatriculasListaOtrasRData $useCase */
    $useCase = DependencyResolver::get(MatriculasListaOtrasRData::class);
    $data = $useCase->execute([
        'apellido1' => $apellido1,
        'esquema_region_stgr' => $esquema,
    ]);
} catch (\Throwable $e) {
    if (MatriculasListaOtrasRData::esAvisoRegionStgr($e)) {
        $problemas = [];
        RegionStgrAviso::registrar($problemas, $e);
        $error = RegionStgrAviso::formatear($problemas);
    } else {
        $error = $e->getMessage();
    }
}

ContestarJson::enviar($error, $data);
