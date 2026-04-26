<?php

use src\asistentes\application\TablaPeticionesData;
use frontend\shared\web\ContestarJson;

$data = TablaPeticionesData::build($_POST);
ContestarJson::enviar('', $data);
