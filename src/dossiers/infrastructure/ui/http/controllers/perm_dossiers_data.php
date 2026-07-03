<?php

use src\dossiers\application\PermDossiersListaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var PermDossiersListaData $useCase */
$useCase = DependencyResolver::get(PermDossiersListaData::class);

$Qtipo = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'tipo');
$tipo = $Qtipo === '' ? 'p' : $Qtipo;
$data = $useCase->build($tipo);
ContestarJson::enviar('', $data);
