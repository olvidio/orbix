<?php

use src\asistentes\application\ActivPendientesSelectData;
use frontend\shared\web\ContestarJson;

$data = ActivPendientesSelectData::build($_POST);
ContestarJson::enviar('', $data);
