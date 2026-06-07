<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

use src\configuracion\application\ModulosUpdateAction;
use src\shared\infrastructure\DependencyResolver;

/** @var ModulosUpdateAction $useCase */
$useCase = DependencyResolver::get(ModulosUpdateAction::class);

header('Content-Type: text/plain; charset=UTF-8');
echo $useCase->execute($_POST);
