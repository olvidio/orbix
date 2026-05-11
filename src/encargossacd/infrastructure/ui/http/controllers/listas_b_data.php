<?php

use src\encargossacd\application\ListasBData;
use src\shared\web\ContestarJson;

$sf = (int)(filter_input(INPUT_POST, 'sf') ?? filter_input(INPUT_GET, 'sf') ?? 0);

ContestarJson::enviar('', ListasBData::execute($sf));
