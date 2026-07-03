<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DireccionesQueData;
use src\shared\web\ContestarJson;

$Qid_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi');
ContestarJson::enviar('', DependencyResolver::get(DireccionesQueData::class)->execute($Qid_ubi));
