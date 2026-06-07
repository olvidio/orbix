<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use src\dossiers\application\PermDossierVerFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var PermDossierVerFormData $useCase */
$useCase = DependencyResolver::get(PermDossierVerFormData::class);

$Qid_tipo_dossier = input_int($_POST, 'id_tipo_dossier');
$Qtipo = input_string($_POST, 'tipo');

$data = $useCase->build($Qid_tipo_dossier, $Qtipo);
ContestarJson::enviar('', $data);
