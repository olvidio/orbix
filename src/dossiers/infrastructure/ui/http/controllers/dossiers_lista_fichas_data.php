<?php

use src\dossiers\application\DossiersListaFichasData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var DossiersListaFichasData $useCase */
$useCase = DependencyResolver::get(DossiersListaFichasData::class);
$data = $useCase->build(
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'pau'),
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_pau'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'obj_pau')
);
ContestarJson::enviar('', $data);
