<?php
/**
 * JSON para {@see \src\configuracion\application\ModulosSelectData}.
 * `hash_lista_html`: {@see \frontend\configuracion\helpers\ModulosSelectRender}.
 */

use src\configuracion\application\ModulosSelectData;
use frontend\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$data = ModulosSelectData::build($_POST);
ContestarJson::enviar('', $data);
