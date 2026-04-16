<?php

use src\zonassacd\application\ZonaSacdLista;
use web\ContestarJson;

$Qid_zona = (string)filter_input(INPUT_POST, 'id_zona');
ContestarJson::enviar('', ZonaSacdLista::execute($Qid_zona));
