<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\TelecoDescLista;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;

$Qid_tipo_teleco = input_int($_POST, 'id_tipo_teleco');
ContestarJson::enviar('', DependencyResolver::get(TelecoDescLista::class)->execute($Qid_tipo_teleco));
