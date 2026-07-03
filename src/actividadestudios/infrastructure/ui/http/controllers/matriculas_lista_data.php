<?php

use src\actividadestudios\application\MatriculasListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$error = '';
$data = [];
try {
    $inicioIso = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'inicioIso');
    $finIso = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'finIso');
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
