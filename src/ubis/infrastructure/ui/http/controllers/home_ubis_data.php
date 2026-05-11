<?php

use src\ubis\application\HomeUbisData;
use src\shared\web\ContestarJson;

$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
ContestarJson::enviar('', HomeUbisData::execute($Qid_ubi));

