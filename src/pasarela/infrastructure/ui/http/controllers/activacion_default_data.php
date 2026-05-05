<?php

use frontend\shared\web\ContestarJson;
use src\pasarela\application\ActivacionDefaultData;

$data = ActivacionDefaultData::execute();
ContestarJson::enviar('', $data);
