<?php

use frontend\shared\PostRequest;

require_once("frontend/shared/global_header_front.inc");

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$Qid_tipo_teleco = (int)filter_input(INPUT_POST, 'id_tipo_teleco');
$Qdesc_teleco = (int)filter_input(INPUT_POST, 'id_desc_teleco');
$Qnum_teleco = (string)filter_input(INPUT_POST, 'num_teleco');
$Qobserv = (string)filter_input(INPUT_POST, 'observ');
$s_pkey = (string)filter_input(INPUT_POST, 's_pkey');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

PostRequest::getDataFromUrl('/src/ubis/teleco_guardar', [
    'obj_pau' => $Qobj_pau,
    'id_ubi' => $Qid_ubi,
    'id_tipo_teleco' => $Qid_tipo_teleco,
    'id_desc_teleco' => $Qdesc_teleco,
    'num_teleco' => $Qnum_teleco,
    'observ' => $Qobserv,
    's_pkey' => $s_pkey,
    'sel' => $a_sel,
]);

echo '';
