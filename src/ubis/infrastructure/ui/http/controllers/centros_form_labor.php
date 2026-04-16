<?php

use src\ubis\application\CentrosFormLaborData;
use web\ContestarJson;

$Qid_ubi = (int)(filter_input(INPUT_POST, 'id_ubi') ?? filter_input(INPUT_GET, 'id_ubi'));
ContestarJson::enviar('', CentrosFormLaborData::execute($Qid_ubi));

