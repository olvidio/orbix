<?php

use src\asistentes\application\ListaUltimaActivData;
use frontend\shared\web\ContestarJson;

$data = ListaUltimaActivData::build($_POST);
ContestarJson::enviar('', $data);
