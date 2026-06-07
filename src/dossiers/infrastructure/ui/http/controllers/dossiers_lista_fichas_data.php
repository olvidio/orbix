<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use src\dossiers\application\DossiersListaFichasData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var DossiersListaFichasData $useCase */
$useCase = DependencyResolver::get(DossiersListaFichasData::class);
$data = $useCase->build(
    input_string($_POST, 'pau'),
    input_int($_POST, 'id_pau'),
    input_string($_POST, 'obj_pau')
);
ContestarJson::enviar('', $data);
