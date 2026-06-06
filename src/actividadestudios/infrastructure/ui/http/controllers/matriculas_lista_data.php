<?php

use src\actividadestudios\application\MatriculasListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;

$error = '';
$data = [];
try {
    $inicioIso = input_string($_POST, 'inicioIso');
    $finIso = input_string($_POST, 'finIso');
    if ($inicioIso === '' || $finIso === '') {
        throw new \InvalidArgumentException(_('Se requieren inicioIso y finIso'));
    }
    /** @var MatriculasListaData $useCase */
    $useCase = DependencyResolver::get(MatriculasListaData::class);
    $data = $useCase->execute(['inicio_iso' => $inicioIso, 'fin_iso' => $finIso]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
