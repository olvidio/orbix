<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string_list;

use src\profesores\application\ListaPorDepartamentos;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ListaPorDepartamentos $useCase */
$useCase = DependencyResolver::get(ListaPorDepartamentos::class);
$data = $useCase->getData(input_string_list($_POST, 'dl'), input_int($_POST, 'filtro'));
ContestarJson::enviar('', $data);
