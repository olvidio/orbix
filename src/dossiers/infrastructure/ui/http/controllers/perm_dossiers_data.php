<?php

use function src\shared\domain\helpers\input_string;

use src\dossiers\application\PermDossiersListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var PermDossiersListaData $useCase */
$useCase = DependencyResolver::get(PermDossiersListaData::class);

$Qtipo = input_string($_POST, 'tipo');
$tipo = $Qtipo === '' ? 'p' : $Qtipo;
$data = $useCase->build($tipo);
ContestarJson::enviar('', $data);
