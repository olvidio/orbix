<?php

use src\encargossacd\application\ListasBData;
use web\ContestarJson;

$sf = (int)(filter_input(INPUT_POST, 'sf') ?? filter_input(INPUT_GET, 'sf') ?? 0);

ContestarJson::enviar('', ListasBData::execute($sf));
