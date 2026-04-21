<?php

use src\misas\application\DesplegableEncargosData;
use web\ContestarJson;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');
$id_enc_raw = filter_input(INPUT_POST, 'id_enc');
$Qid_enc = ($id_enc_raw === null || $id_enc_raw === '') ? null : (int)$id_enc_raw;

ContestarJson::enviar('', DesplegableEncargosData::getData($Qid_zona, $Qid_enc));
