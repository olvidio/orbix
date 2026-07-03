<?php

/**
 * JSON para {@see \src\configuracion\application\ModulosFormData}.
 * HTML de hash: {@see \frontend\configuracion\helpers\ModulosFormRender}.
 */

use src\configuracion\application\ModulosFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;


/** @var ModulosFormData $useCase */
$useCase = DependencyResolver::get(ModulosFormData::class);
$data = $useCase->execute($_POST);
ContestarJson::enviar('', $data);
