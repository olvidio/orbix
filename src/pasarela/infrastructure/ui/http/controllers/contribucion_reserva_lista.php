<?php

use src\shared\web\ContestarJson;
use src\pasarela\application\ContribucionReservaLista;

$data = ContribucionReservaLista::execute();
ContestarJson::enviar('', $data);
