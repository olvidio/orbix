<?php

use src\encargossacd\application\ListasComSacdData;
use src\shared\web\ContestarJson;

$sel = (string)(filter_input(INPUT_POST, 'sel') ?? filter_input(INPUT_GET, 'sel') ?? '');

ContestarJson::enviar('', ListasComSacdData::execute($sel));
