<?php

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
echo PostRequest::sendRawPost('/src/configuracion/modulos_update', PostRequest::requestPayloadForHash());
