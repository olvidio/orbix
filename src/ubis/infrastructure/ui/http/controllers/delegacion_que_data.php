<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DelegacionQueData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', DependencyResolver::get(DelegacionQueData::class)->execute());
