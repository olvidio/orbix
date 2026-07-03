<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\TelecoDescLista;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$Qid_tipo_teleco = FuncTablasSupport::inputInt($_POST, 'id_tipo_teleco');
ContestarJson::enviar('', DependencyResolver::get(TelecoDescLista::class)->execute($Qid_tipo_teleco));
