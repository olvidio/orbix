<?php

use frontend\shared\helpers\AjaxJsonSupport;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
AjaxJsonSupport::fromPlainText(trim(PostRequest::sendRawPost('/src/configuracion/modulos_update', PostRequest::requestPayloadForHash())));
