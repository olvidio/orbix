<?php

use src\ubis\application\ListCtrData;
use src\shared\web\ContestarJson;

$Qque_lista = (string)filter_input(INPUT_POST, 'que_lista');
$Qloc = (string)filter_input(INPUT_POST, 'loc');
$Qid_sel = (string)filter_input(INPUT_POST, 'id_sel');
$Qscroll_id = (string)filter_input(INPUT_POST, 'scroll_id');

ContestarJson::enviar('', ListCtrData::execute($Qloc, $Qque_lista, $Qid_sel, $Qscroll_id));
