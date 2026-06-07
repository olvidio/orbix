<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
/**
 * JSON para {@see \src\configuracion\application\ModulosFormData}.
 * HTML de hash: {@see \frontend\configuracion\helpers\ModulosFormRender}.
 */

use src\configuracion\application\ModulosFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

/** @var ModulosFormData $useCase */
$useCase = DependencyResolver::get(ModulosFormData::class);
$data = $useCase->execute($_POST);
ContestarJson::enviar('', $data);
