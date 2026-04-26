<?php

use src\asistentes\application\ListaActivCtrData;
use frontend\shared\web\ContestarJson;

$data = ListaActivCtrData::build($_POST);
ContestarJson::enviar('', $data);
