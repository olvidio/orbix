<?php

use src\asistentes\application\ListaUltimQueCtrData;
use frontend\shared\web\ContestarJson;

$data = ListaUltimQueCtrData::build($_POST);
ContestarJson::enviar('', $data);
