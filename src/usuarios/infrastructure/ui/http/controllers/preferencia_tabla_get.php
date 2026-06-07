<?php

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\PreferenciaTablaData;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_string;

$id_tabla = input_string($_POST, 'id_tabla');

/** @var PreferenciaTablaData $useCase */
$useCase = DependencyResolver::get(PreferenciaTablaData::class);
$data = $useCase->execute($id_tabla);

ContestarJson::enviar('', $data);
