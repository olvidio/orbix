<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\HomeUbisData;
use src\shared\web\ContestarJson;

$Qid_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi');
ContestarJson::enviar('', DependencyResolver::get(HomeUbisData::class)->execute($Qid_ubi));
