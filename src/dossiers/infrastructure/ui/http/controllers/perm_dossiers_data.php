<?php

use src\dossiers\application\PermDossiersListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

/** @var PermDossiersListaData $useCase */
$useCase = DependencyResolver::get(PermDossiersListaData::class);

$Qtipo = FuncTablasSupport::inputString($_POST, 'tipo');
$tipo = $Qtipo === '' ? 'p' : $Qtipo;
$data = $useCase->build($tipo);
ContestarJson::enviar('', $data);
