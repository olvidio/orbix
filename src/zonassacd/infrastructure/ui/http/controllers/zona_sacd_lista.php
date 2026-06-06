<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaSacdLista;
use function src\shared\domain\helpers\input_string;

$input = ['id_zona' => input_string($_POST, 'id_zona')];

/** @var ZonaSacdLista $useCase */
$useCase = DependencyResolver::get(ZonaSacdLista::class);
ContestarJson::enviar('', $useCase->execute($input['id_zona']));
