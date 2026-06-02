<?php

use src\asistentes\application\ListaEstCtrData;
use src\shared\web\ContestarJson;

$data = $GLOBALS['container']->get(ListaEstCtrData::class)->build($_POST);
ContestarJson::enviar('', $data);
