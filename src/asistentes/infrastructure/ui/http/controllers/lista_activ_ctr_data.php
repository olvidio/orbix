<?php

use src\asistentes\application\ListaActivCtrData;
use src\shared\web\ContestarJson;

$data = $GLOBALS['container']->get(ListaActivCtrData::class)->build($_POST);
ContestarJson::enviar('', $data);
