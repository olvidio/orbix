<?php

use src\configuracion\application\ModulosSelectData;
use frontend\shared\web\ContestarJson;

$data = ModulosSelectData::build($_POST);
ContestarJson::enviar('', $data);
