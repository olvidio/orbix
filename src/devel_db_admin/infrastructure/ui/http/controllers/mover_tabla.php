<?php

declare(strict_types=1);

/**
 * Lista esquemas con la tabla y ejecuta {@see MoverTabla} (POST: `tabla`).
 * JSON `data`: `{ "a_esquemas": string[], "lines": string[] }` o error con `success: false`.
 */

use frontend\shared\web\ContestarJson;
use src\devel_db_admin\application\MoverTabla;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;

require_once 'frontend/shared/global_header_front.inc';

$tabla = (string) filter_input(INPUT_POST, 'tabla');

$dbp = new DBPropiedades();
$raw = $dbp->array_esquemas_con_tabla($tabla);
$a_esquemas = is_array($raw) ? array_values($raw) : [];

$result = (new MoverTabla())->ejecutar($tabla, $a_esquemas);

if ($result->fatalError !== '') {
    ContestarJson::enviar($result->fatalError, 'none');

    return;
}

ContestarJson::enviar('', [
    'a_esquemas' => $a_esquemas,
    'lines' => $result->lines,
]);
