<?php

use src\asistentes\application\ListaAsistentesData;
use src\shared\web\ContestarJson;

$data = $GLOBALS['container']->get(ListaAsistentesData::class)->build($_POST);
ContestarJson::enviar('', $data);
