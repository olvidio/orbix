<?php

use src\asistentes\application\ListaEstCtrData;
use frontend\shared\web\ContestarJson;

$data = ListaEstCtrData::build($_POST);
ContestarJson::enviar('', $data);
