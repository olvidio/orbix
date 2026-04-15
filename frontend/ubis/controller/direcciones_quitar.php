<?php

use frontend\shared\PostRequest;

require_once("frontend/shared/global_header_front.inc");

PostRequest::getDataFromUrl('/src/ubis/direcciones_quitar', [
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'idx' => (int)filter_input(INPUT_POST, 'idx'),
    'obj_dir' => (string)filter_input(INPUT_POST, 'obj_dir'),
    'id_direccion' => (string)filter_input(INPUT_POST, 'id_direccion'),
]);

echo '';
