<?php

use src\configuracion\application\ModulosFormData;
use frontend\shared\web\ContestarJson;

$data = ModulosFormData::build($_POST);
ContestarJson::enviar('', $data);
