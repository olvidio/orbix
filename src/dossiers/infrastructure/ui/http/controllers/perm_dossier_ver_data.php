<?php

use src\dossiers\application\PermDossierVerFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var PermDossierVerFormData $useCase */
$useCase = DependencyResolver::get(PermDossierVerFormData::class);

$Qid_tipo_dossier = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_tipo_dossier');
$Qtipo = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'tipo');

$data = $useCase->build($Qid_tipo_dossier, $Qtipo);
ContestarJson::enviar('', $data);
