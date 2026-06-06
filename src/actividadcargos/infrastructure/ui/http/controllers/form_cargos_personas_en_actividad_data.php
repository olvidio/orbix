<?php

use src\actividadcargos\application\FormCargosPersonasEnActividadData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var FormCargosPersonasEnActividadData $builder */
$builder = DependencyResolver::get(FormCargosPersonasEnActividadData::class);
$result = $builder->build($_POST);
$errorVal = $result['error'] ?? '';
$error = is_string($errorVal) ? $errorVal : '';
unset($result['error']);
ContestarJson::enviar($error, $result);
