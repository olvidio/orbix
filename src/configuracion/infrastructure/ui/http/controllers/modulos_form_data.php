<?php
/**
 * JSON para {@see \src\configuracion\application\ModulosFormData}.
 * HTML de hash: {@see \frontend\configuracion\helpers\ModulosFormRender}.
 */

use src\configuracion\application\ModulosFormData;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$data = ModulosFormData::build($_POST);
ContestarJson::enviar('', $data);
