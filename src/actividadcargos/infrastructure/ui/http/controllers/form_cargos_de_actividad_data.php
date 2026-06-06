<?php

use src\actividadcargos\application\FormCargosDeActividadData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var FormCargosDeActividadData $builder */
$builder = DependencyResolver::get(FormCargosDeActividadData::class);
$result = $builder->build($_POST);
$errorRaw = $result['error'] ?? '';
$error = is_string($errorRaw) ? $errorRaw : (is_scalar($errorRaw) ? (string) $errorRaw : '');
unset($result['error']);
ContestarJson::enviar($error, $result);
