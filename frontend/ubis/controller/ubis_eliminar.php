<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$data = ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/ubis_eliminar', [
    'obj_pau' => (string)filter_input(INPUT_POST, 'obj_pau'),
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
]));
$error = ubis_api_error($data);
if ($error !== '') {
    echo $error;
}
