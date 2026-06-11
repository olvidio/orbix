<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

FrontBootstrap::boot();
PostRequest::getDataFromUrl('/src/ubis/direcciones_asignar', [
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'obj_dir' => (string)filter_input(INPUT_POST, 'obj_dir'),
    'id_direccion' => (int)filter_input(INPUT_POST, 'id_direccion'),
]);

ajax_json_response();
