<?php

use src\asistentes\application\ListaEstCtrData;
use src\shared\web\ContestarJson;

$data = ListaEstCtrData::build($_POST);
ContestarJson::enviar('', $data);
