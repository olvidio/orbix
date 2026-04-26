<?php

use src\encargossacd\application\ListasAData;
use frontend\shared\web\ContestarJson;

$sf = (int)(filter_input(INPUT_POST, 'sf') ?? filter_input(INPUT_GET, 'sf') ?? 0);

ContestarJson::enviar('', ListasAData::execute($sf));
