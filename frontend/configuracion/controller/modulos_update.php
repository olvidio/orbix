<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/ajax_json_support.php';

FrontBootstrap::boot();
ajax_json_from_plain_text(trim(PostRequest::sendRawPost('/src/configuracion/modulos_update', PostRequest::requestPayloadForHash())));
