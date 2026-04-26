<?php

use src\asistentes\application\ListaAsistentesData;
use frontend\shared\web\ContestarJson;

$data = ListaAsistentesData::build($_POST);
ContestarJson::enviar('', $data);
