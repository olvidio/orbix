<?php

use src\encargossacd\application\ListasExigenciaCtrData;
use frontend\shared\web\ContestarJson;

$sf = (int)(filter_input(INPUT_POST, 'sf') ?? filter_input(INPUT_GET, 'sf') ?? 0);
$ctr_igl = (string)(filter_input(INPUT_POST, 'ctr_igl') ?? filter_input(INPUT_GET, 'ctr_igl') ?? '');

ContestarJson::enviar('', ListasExigenciaCtrData::execute($sf, $ctr_igl));
