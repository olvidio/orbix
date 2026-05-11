<?php

use src\shared\web\ContestarJson;
use src\pasarela\application\ContribucionNoDuermeDefaultData;

$data = ContribucionNoDuermeDefaultData::execute();
ContestarJson::enviar('', $data);
