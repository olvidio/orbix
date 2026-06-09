<?php

declare(strict_types=1);

/**
 * JSON para {@see \src\devel_db_admin\application\DbPropiedadesFormData}.
 */

use src\shared\web\ContestarJson;
use src\devel_db_admin\application\DbPropiedadesFormData;
use src\shared\infrastructure\DependencyResolver;


/** @var DbPropiedadesFormData $data */
$data = DependencyResolver::get(DbPropiedadesFormData::class);
$payload = $data->build($_POST);
if (isset($payload['error'])) {
    $error = is_scalar($payload['error']) ? (string) $payload['error'] : '';
    ContestarJson::enviar($error, 'none');

    return;
}

ContestarJson::enviar('', $payload);
