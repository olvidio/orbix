<?php

use src\ubis\application\HomeUbisData;
use web\ContestarJson;

$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
ContestarJson::enviar('', HomeUbisData::execute($Qid_ubi));

