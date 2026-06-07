<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\TelecoTablaData;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$Qobj_pau = input_string($_POST, 'obj_pau');
$Qid_ubi = input_int($_POST, 'id_ubi');

ContestarJson::enviar('', DependencyResolver::get(TelecoTablaData::class)->execute($Qobj_pau, $Qid_ubi));
