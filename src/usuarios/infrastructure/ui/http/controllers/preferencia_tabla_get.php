<?php

use src\shared\infrastructure\DependencyResolver;
use src\usuarios\application\PreferenciaTablaData;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$id_tabla = FuncTablasSupport::inputString($_POST, 'id_tabla');

/** @var PreferenciaTablaData $useCase */
$useCase = DependencyResolver::get(PreferenciaTablaData::class);
$data = $useCase->execute($id_tabla);

ContestarJson::enviar('', $data);
