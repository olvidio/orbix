<?php

use src\dossiers\application\PermDossierVerFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

/** @var PermDossierVerFormData $useCase */
$useCase = DependencyResolver::get(PermDossierVerFormData::class);

$Qid_tipo_dossier = FuncTablasSupport::inputInt($_POST, 'id_tipo_dossier');
$Qtipo = FuncTablasSupport::inputString($_POST, 'tipo');

$data = $useCase->build($Qid_tipo_dossier, $Qtipo);
ContestarJson::enviar('', $data);
