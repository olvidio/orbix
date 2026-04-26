<?php

use src\asistentes\application\AsistenteMoverData;
use frontend\shared\web\ContestarJson;

$data = AsistenteMoverData::build($_POST);
ContestarJson::enviar('', $data);
