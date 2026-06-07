<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CentrosGetNumData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', DependencyResolver::get(CentrosGetNumData::class)->execute());

