<?php

use src\asistentes\application\FormActividadesDeUnaPersonaData;
use frontend\shared\web\ContestarJson;

$data = FormActividadesDeUnaPersonaData::build($_POST);
ContestarJson::enviar('', $data);
