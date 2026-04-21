<?php

use src\encargossacd\application\ListasDData;
use web\ContestarJson;

$sf = (int)(filter_input(INPUT_POST, 'sf') ?? filter_input(INPUT_GET, 'sf') ?? 0);

ContestarJson::enviar('', ListasDData::execute($sf));
