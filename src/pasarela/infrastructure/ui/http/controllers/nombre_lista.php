<?php

use frontend\shared\web\ContestarJson;
use src\pasarela\application\NombreLista;

$data = NombreLista::execute();
ContestarJson::enviar('', $data);
