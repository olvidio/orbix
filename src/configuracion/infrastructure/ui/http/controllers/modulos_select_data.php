<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
/**
 * JSON para {@see \src\configuracion\application\ModulosSelectData}.
 * `hash_lista_html`: {@see \frontend\configuracion\helpers\ModulosSelectRender}.
 */

use src\configuracion\application\ModulosSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;


/** @var ModulosSelectData $useCase */
$useCase = DependencyResolver::get(ModulosSelectData::class);
$data = $useCase->execute($_POST);
ContestarJson::enviar('', $data);
