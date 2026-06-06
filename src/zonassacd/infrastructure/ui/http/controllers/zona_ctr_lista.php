<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaCtrLista;
use function src\shared\domain\helpers\input_string;

$input = ['id_zona' => input_string($_POST, 'id_zona')];

/** @var ZonaCtrLista $useCase */
$useCase = DependencyResolver::get(ZonaCtrLista::class);
ContestarJson::enviar('', $useCase->execute($input['id_zona']));
