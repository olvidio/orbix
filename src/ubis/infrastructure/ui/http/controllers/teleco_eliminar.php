<?php

use src\ubis\application\TelecoEliminar;
use web\ContestarJson;
use function core\urlsafe_b64decode;

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$a_pkey = [];
if (!empty($a_sel)) {
    $s = explode('#', $a_sel[0])[0] ?? '';
    $s = str_replace("'", '"', $s);
    $a_pkey = (array)json_decode(urlsafe_b64decode($s));
}

ContestarJson::enviar('', TelecoEliminar::execute($Qobj_pau, $a_pkey));
