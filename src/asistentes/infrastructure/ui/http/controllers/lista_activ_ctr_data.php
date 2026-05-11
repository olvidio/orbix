<?php

use src\asistentes\application\ListaActivCtrData;
use src\shared\web\ContestarJson;

$data = ListaActivCtrData::build($_POST);
ContestarJson::enviar('', $data);
