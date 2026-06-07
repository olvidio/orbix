<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CentrosGetPlazasData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', DependencyResolver::get(CentrosGetPlazasData::class)->execute());

