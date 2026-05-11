<?php

use src\ubis\application\CentrosFormData;
use src\shared\web\ContestarJson;

$Qid_ubi = (int)(filter_input(INPUT_POST, 'id_ubi') ?? filter_input(INPUT_GET, 'id_ubi'));
ContestarJson::enviar('', CentrosFormData::execute($Qid_ubi, CentrosFormData::MODO_PLAZAS));
