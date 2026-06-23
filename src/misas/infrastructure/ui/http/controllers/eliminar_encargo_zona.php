<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\EliminarEncargoZona;
use src\shared\web\ContestarJson;

$Qid_enc = (int)filter_post('id_enc', FILTER_VALIDATE_INT);

/** @var EliminarEncargoZona $useCase */
$useCase = DependencyResolver::get(EliminarEncargoZona::class);
$result = $useCase->execute($Qid_enc);

ContestarJson::enviar($result, ['id_enc' => $Qid_enc]);
