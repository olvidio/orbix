<?php

use src\asistentes\application\QueCtrListaData;
use frontend\shared\web\ContestarJson;

$data = QueCtrListaData::build($_POST);
ContestarJson::enviar('', $data);
