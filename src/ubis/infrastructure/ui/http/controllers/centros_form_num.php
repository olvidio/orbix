<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CentrosFormData;
use src\shared\web\ContestarJson;

$input = array_merge($_GET, $_POST);
$Qid_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ubi');
ContestarJson::enviar('', DependencyResolver::get(CentrosFormData::class)->execute($Qid_ubi, CentrosFormData::MODO_NUM));
