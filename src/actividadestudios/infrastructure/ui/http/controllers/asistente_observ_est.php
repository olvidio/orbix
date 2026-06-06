<?php

use src\actividadestudios\application\AsistenteObservEst;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var AsistenteObservEst $useCase */
$useCase = DependencyResolver::get(AsistenteObservEst::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
