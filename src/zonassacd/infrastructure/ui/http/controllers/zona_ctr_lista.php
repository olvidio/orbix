<?php

use src\zonassacd\application\ZonaCtrLista;
use src\shared\web\ContestarJson;

$Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
ContestarJson::enviar('', ZonaCtrLista::execute($Qid_zona));
