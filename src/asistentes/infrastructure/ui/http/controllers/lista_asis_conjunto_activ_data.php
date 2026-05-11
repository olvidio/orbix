<?php

use src\asistentes\application\ListaAsisConjuntoActivData;
use src\shared\web\ContestarJson;

$data = ListaAsisConjuntoActivData::build($_POST);
ContestarJson::enviar('', $data);
