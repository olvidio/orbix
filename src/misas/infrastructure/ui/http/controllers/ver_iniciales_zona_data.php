<?php

use src\misas\application\VerInicialesZonaData;
use src\shared\web\ContestarJson;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');

ContestarJson::enviar('', VerInicialesZonaData::getData($Qid_zona));
