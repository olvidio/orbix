<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DireccionesQueData;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;

$Qid_ubi = input_int($_POST, 'id_ubi');
ContestarJson::enviar('', DependencyResolver::get(DireccionesQueData::class)->execute($Qid_ubi));
