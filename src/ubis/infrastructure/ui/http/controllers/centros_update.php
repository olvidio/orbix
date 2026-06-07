<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CentrosUpdate;

header('Content-Type: text/plain; charset=UTF-8');
echo DependencyResolver::get(CentrosUpdate::class)->execute($_POST);
