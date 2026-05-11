<?php

use src\misas\application\VerEncargosCentrosData;
use src\shared\web\ContestarJson;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');

ContestarJson::enviar('', VerEncargosCentrosData::getData($Qid_zona));
