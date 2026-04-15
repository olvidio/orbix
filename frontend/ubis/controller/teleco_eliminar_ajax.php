<?php

use frontend\shared\PostRequest;

require_once("frontend/shared/global_header_front.inc");

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

PostRequest::getDataFromUrl('/src/ubis/teleco_eliminar', [
    'obj_pau' => $Qobj_pau,
    'sel' => $a_sel,
]);

echo '';
