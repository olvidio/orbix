<?php

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\PreferenciaTablaData;
use src\shared\web\ContestarJson;

$id_tabla = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_tabla');

/** @var PreferenciaTablaData $useCase */
$useCase = DependencyResolver::get(PreferenciaTablaData::class);
$data = $useCase->execute($id_tabla);

ContestarJson::enviar('', $data);
