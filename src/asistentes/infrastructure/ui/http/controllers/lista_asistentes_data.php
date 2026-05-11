<?php

use src\asistentes\application\ListaAsistentesData;
use src\shared\web\ContestarJson;

$data = ListaAsistentesData::build($_POST);
ContestarJson::enviar('', $data);
