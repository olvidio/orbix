<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisBuscarOpcionesData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', DependencyResolver::get(UbisBuscarOpcionesData::class)->execute());
