<?php

use src\asistentes\application\ListaAsisConjuntoActivData;
use src\shared\web\ContestarJson;

$data = $GLOBALS['container']->get(ListaAsisConjuntoActivData::class)->build($_POST);
ContestarJson::enviar('', $data);
