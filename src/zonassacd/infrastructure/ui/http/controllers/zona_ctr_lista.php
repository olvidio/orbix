<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaCtrLista;
use src\shared\domain\helpers\FuncTablasSupport;
$input = ['id_zona' => FuncTablasSupport::inputString($_POST, 'id_zona')];

/** @var ZonaCtrLista $useCase */
$useCase = DependencyResolver::get(ZonaCtrLista::class);
ContestarJson::enviar('', $useCase->execute($input['id_zona']));
