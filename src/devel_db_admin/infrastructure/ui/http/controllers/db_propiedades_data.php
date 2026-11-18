<?php

declare(strict_types=1);

/**
 * JSON para {@see \src\devel_db_admin\application\DbPropiedadesFormData}.
 */

use src\shared\web\ContestarJson;
use src\devel_db_admin\application\DbPropiedadesFormData;
use src\shared\infrastructure\DependencyResolver;
use src\devel_db_admin\infrastructure\ui\http\PermisoHerramientasBdGuard;

// Solo quien ve las herramientas de base de datos en el menú (PermisoHerramientasBd).
if (!PermisoHerramientasBdGuard::permiteEsquemas()) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}


/** @var DbPropiedadesFormData $data */
$data = DependencyResolver::get(DbPropiedadesFormData::class);
$payload = $data->build($_POST);
if (isset($payload['error'])) {
    $error = is_scalar($payload['error']) ? (string) $payload['error'] : '';
    ContestarJson::enviar($error, 'none');

    return;
}

ContestarJson::enviar('', $payload);
