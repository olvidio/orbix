<?php

use src\asistentes\application\ListaAsisConjuntoActivData;
use frontend\shared\web\ContestarJson;

$data = ListaAsisConjuntoActivData::build($_POST);
ContestarJson::enviar('', $data);
