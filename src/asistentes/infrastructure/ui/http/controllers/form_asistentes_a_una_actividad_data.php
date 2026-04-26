<?php

use src\asistentes\application\FormAsistentesAUnaActividadData;
use frontend\shared\web\ContestarJson;

$data = FormAsistentesAUnaActividadData::build($_POST);
ContestarJson::enviar('', $data);
