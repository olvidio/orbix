<?php

use src\actividadescentro\application\ActivCtrShellData;
use frontend\shared\web\ContestarJson;

$data = ActivCtrShellData::build($_POST);
ContestarJson::enviar('', $data);
