<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CentrosFormData;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;

$input = array_merge($_GET, $_POST);
$Qid_ubi = input_int($input, 'id_ubi');
ContestarJson::enviar('', DependencyResolver::get(CentrosFormData::class)->execute($Qid_ubi, CentrosFormData::MODO_PLAZAS));
