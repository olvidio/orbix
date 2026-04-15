<?php

use frontend\shared\PostRequest;

require_once("frontend/shared/global_header_front.inc");

PostRequest::getDataFromUrl('/src/ubis/direcciones_asignar', [
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'obj_dir' => (string)filter_input(INPUT_POST, 'obj_dir'),
    'id_direccion' => (int)filter_input(INPUT_POST, 'id_direccion'),
]);

echo '';
