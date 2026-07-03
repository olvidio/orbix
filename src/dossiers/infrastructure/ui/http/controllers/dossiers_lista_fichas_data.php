<?php

use src\dossiers\application\DossiersListaFichasData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

/** @var DossiersListaFichasData $useCase */
$useCase = DependencyResolver::get(DossiersListaFichasData::class);
$data = $useCase->build(
    FuncTablasSupport::inputString($_POST, 'pau'),
    FuncTablasSupport::inputInt($_POST, 'id_pau'),
    FuncTablasSupport::inputString($_POST, 'obj_pau')
);
ContestarJson::enviar('', $data);
