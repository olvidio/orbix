<?php

use src\procesos\application\TipoActivProcesoLstPosibles;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var TipoActivProcesoLstPosibles $useCase */
$useCase = DependencyResolver::get(TipoActivProcesoLstPosibles::class);

ContestarJson::enviar('', $useCase->execute($_POST));
