<?php

use src\ubis\application\TelecoEditarData;
use web\ContestarJson;
use function core\urlsafe_b64decode;


$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qmod = (string)filter_input(INPUT_POST, 'mod');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$s_pkey = (string)filter_input(INPUT_POST, 's_pkey');

$a_pkey = [];
if (!empty($a_sel)) {
    $s = explode('#', $a_sel[0])[0] ?? '';
    $s = str_replace("'", '"', $s);
    $a_pkey = (array)json_decode(urlsafe_b64decode($s));
} elseif (!empty($s_pkey)) {
    $a_pkey = (array)json_decode(urlsafe_b64decode($s_pkey));
}
// Entiendo que solo hay uno
if (empty($a_pkey[0]) || !is_int($a_pkey[0])) {
    $pkey = 0;
} else {
    $pkey = $a_pkey[0];
}
ContestarJson::enviar('', TelecoEditarData::execute($Qobj_pau, $Qmod, $Qid_ubi, $pkey));
