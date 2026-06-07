<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CentrosGetLaborData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', DependencyResolver::get(CentrosGetLaborData::class)->execute());

