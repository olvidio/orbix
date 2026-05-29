<?php

use src\misas\application\DesplegableCentrosZonaData;
use src\shared\web\ContestarJson;

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');
$id_ubi_raw = filter_input(INPUT_POST, 'id_ubi');
$Qid_ubi = ($id_ubi_raw === null || $id_ubi_raw === '') ? null : (int)$id_ubi_raw;

ContestarJson::enviar('', DesplegableCentrosZonaData::getData($Qid_zona, $Qid_ubi));
