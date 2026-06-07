<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubiscamas\application\HabitacionFormData;

/** @var HabitacionFormData $useCase */
$useCase = DependencyResolver::get(HabitacionFormData::class);
ContestarJson::enviar('', $useCase->execute($_POST));
