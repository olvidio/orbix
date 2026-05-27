<?php

declare(strict_types=1);

use frontend\shared\PostRequest;

require_once 'frontend/shared/global_header_front.inc';

header('Content-Type: application/json; charset=UTF-8');
echo PostRequest::sendRawPost('/src/devel_db_admin/apptables_update', PostRequest::requestPayloadForHash());
