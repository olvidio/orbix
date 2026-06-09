<?php

declare(strict_types=1);

use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
header('Content-Type: application/json; charset=UTF-8');
echo PostRequest::sendRawPost('/src/devel_db_admin/apptables_update', PostRequest::requestPayloadForHash());
