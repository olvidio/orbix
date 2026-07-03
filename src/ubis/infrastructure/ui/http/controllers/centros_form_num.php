<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CentrosFormData;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$input = array_merge($_GET, $_POST);
$Qid_ubi = FuncTablasSupport::inputInt($input, 'id_ubi');
ContestarJson::enviar('', DependencyResolver::get(CentrosFormData::class)->execute($Qid_ubi, CentrosFormData::MODO_NUM));
