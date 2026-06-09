<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = PostRequest::getDataFromUrl('/src/ubis/ubis_eliminar', [
    'obj_pau' => (string)filter_input(INPUT_POST, 'obj_pau'),
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
]);
if (!empty($data['error'])) {
    echo $data['error'];
}
