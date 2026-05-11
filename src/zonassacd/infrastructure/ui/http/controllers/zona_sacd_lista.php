<?php

use src\zonassacd\application\ZonaSacdLista;
use src\shared\web\ContestarJson;

$Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
ContestarJson::enviar('', ZonaSacdLista::execute($Qid_zona));
