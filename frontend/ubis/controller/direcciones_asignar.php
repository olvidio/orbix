<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
PostRequest::getDataFromUrl('/src/ubis/direcciones_asignar', [
    'ctx_asignar' => (string)filter_input(INPUT_POST, 'ctx_asignar'),
]);

AjaxJsonSupport::response();
