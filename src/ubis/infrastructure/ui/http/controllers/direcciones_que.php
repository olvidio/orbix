<?php

use src\ubis\application\DireccionesQueData;
use src\shared\web\ContestarJson;

$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
ContestarJson::enviar('', DireccionesQueData::execute($Qid_ubi));
