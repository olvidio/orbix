<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\EliminarEncargoZona;
use src\shared\web\ContestarJson;

$Qid_enc = (int)\src\shared\domain\helpers\FilterPostGet::post('id_enc', FILTER_VALIDATE_INT);

/** @var EliminarEncargoZona $useCase */
$useCase = DependencyResolver::get(EliminarEncargoZona::class);
$result = $useCase->execute($Qid_enc);

ContestarJson::enviar($result, ['id_enc' => $Qid_enc]);
