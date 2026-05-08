<?php

declare(strict_types=1);

/**
 * JSON para {@see \src\devel_db_admin\application\DbPropiedadesFormData}.
 */

use frontend\shared\web\ContestarJson;
use src\devel_db_admin\application\DbPropiedadesFormData;

require_once 'frontend/shared/global_header_front.inc';

$payload = DbPropiedadesFormData::build($_POST);
if (isset($payload['error'])) {
    ContestarJson::enviar((string) $payload['error'], 'none');

    return;
}

ContestarJson::enviar('', $payload);
