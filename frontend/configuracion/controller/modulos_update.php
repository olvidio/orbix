<?php

use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

echo PostRequest::sendRawPost('/src/configuracion/modulos_update', $_POST);
