<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\HomeUbisData;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$Qid_ubi = FuncTablasSupport::inputInt($_POST, 'id_ubi');
ContestarJson::enviar('', DependencyResolver::get(HomeUbisData::class)->execute($Qid_ubi));
