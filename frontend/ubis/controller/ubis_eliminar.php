<?php

use frontend\shared\PostRequest;

require_once("frontend/shared/global_header_front.inc");

$data = PostRequest::getDataFromUrl('/src/ubis/ubis_eliminar', [
    'obj_pau' => (string)filter_input(INPUT_POST, 'obj_pau'),
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
]);
if (!empty($data['error'])) {
    echo $data['error'];
}
