<?php

use src\asistentes\application\ListaUltimaActivData;
use src\shared\web\ContestarJson;

$data = ListaUltimaActivData::build($_POST);
ContestarJson::enviar('', $data);
