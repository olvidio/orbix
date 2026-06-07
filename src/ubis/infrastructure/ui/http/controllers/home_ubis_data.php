<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\HomeUbisData;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;

$Qid_ubi = input_int($_POST, 'id_ubi');
ContestarJson::enviar('', DependencyResolver::get(HomeUbisData::class)->execute($Qid_ubi));
