<?php

use frontend\shared\web\ContestarJson;
use src\pasarela\application\ActivacionLista;

$data = ActivacionLista::execute();
ContestarJson::enviar('', $data);
