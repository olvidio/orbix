<?php

use src\actividadestudios\application\AsistenteObserv;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var AsistenteObserv $useCase */
$useCase = DependencyResolver::get(AsistenteObserv::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
