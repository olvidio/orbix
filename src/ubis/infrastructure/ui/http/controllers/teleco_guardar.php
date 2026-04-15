<?php

use src\ubis\application\TelecoGuardar;
use web\ContestarJson;
use function core\urlsafe_b64decode;

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qid_tipo_teleco = (int)filter_input(INPUT_POST, 'id_tipo_teleco');
$Qdesc_teleco = (int)filter_input(INPUT_POST, 'id_desc_teleco');
$Qnum_teleco = (string)filter_input(INPUT_POST, 'num_teleco');
$Qobserv = (string)filter_input(INPUT_POST, 'observ');
$s_pkey = (string)filter_input(INPUT_POST, 's_pkey');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$a_pkey = [];
if (!empty($a_sel)) {
    $s = explode('#', $a_sel[0])[0] ?? '';
    $s = str_replace("'", '"', $s);
    $a_pkey = (array)json_decode(urlsafe_b64decode($s));
} elseif (!empty($s_pkey)) {
    $a_pkey = (array)json_decode(urlsafe_b64decode($s_pkey));
}

$jsondata = ContestarJson::respuestaPhp('', TelecoGuardar::execute(
    $Qobj_pau,
    $Qid_ubi,
    $a_pkey,
    $Qid_tipo_teleco,
    $Qdesc_teleco,
    $Qnum_teleco,
    $Qobserv
));
ContestarJson::send($jsondata);
