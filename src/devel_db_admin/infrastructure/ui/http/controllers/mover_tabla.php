<?php

declare(strict_types=1);

/**
 * Lista esquemas con la tabla y ejecuta {@see MoverTabla} (POST: `tabla`).
 * JSON `data`: `{ "a_esquemas": string[], "lines": string[] }` o error con `success: false`.
 */

use src\shared\web\ContestarJson;
use src\devel_db_admin\application\MoverTabla;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;


$tablaRaw = filter_post('tabla');
$tabla = is_scalar($tablaRaw) ? (string) $tablaRaw : '';

$dbp = new DBPropiedades();
$raw = $dbp->array_esquemas_con_tabla($tabla);
$a_esquemas = [];
if (is_array($raw)) {
    foreach ($raw as $esquema) {
        if ($esquema !== '') {
            $a_esquemas[] = $esquema;
        }
    }
}

$result = (new MoverTabla())->ejecutar($tabla, $a_esquemas);

if ($result->fatalError !== '') {
    ContestarJson::enviar($result->fatalError, 'none');

    return;
}

ContestarJson::enviar('', [
    'a_esquemas' => $a_esquemas,
    'lines' => $result->lines,
]);
