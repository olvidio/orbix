<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\TelecoDescLista;
use src\shared\web\ContestarJson;

$Qid_tipo_teleco = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_tipo_teleco');
ContestarJson::enviar('', DependencyResolver::get(TelecoDescLista::class)->execute($Qid_tipo_teleco));
