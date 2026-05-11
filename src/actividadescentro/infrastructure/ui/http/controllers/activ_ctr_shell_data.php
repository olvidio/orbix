<?php

use src\actividadescentro\application\ActivCtrShellData;
use src\shared\web\ContestarJson;

$data = ActivCtrShellData::build($_POST);
ContestarJson::enviar('', $data);
