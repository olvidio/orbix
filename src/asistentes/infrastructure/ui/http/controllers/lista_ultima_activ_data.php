<?php

use src\asistentes\application\ListaUltimaActivData;
use src\shared\web\ContestarJson;

$data = $GLOBALS['container']->get(ListaUltimaActivData::class)->build($_POST);
ContestarJson::enviar('', $data);
