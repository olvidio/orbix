<?php

use src\encargossacd\application\ListasComCtrData;
use web\ContestarJson;

$sfsv = (string)(filter_input(INPUT_POST, 'sfsv') ?? filter_input(INPUT_GET, 'sfsv') ?? '');

ContestarJson::enviar('', ListasComCtrData::execute($sfsv));
