<?php

use src\shared\web\ContestarJson;
use src\pasarela\application\ContribucionReservaDefaultData;

$data = ContribucionReservaDefaultData::execute();
ContestarJson::enviar('', $data);
