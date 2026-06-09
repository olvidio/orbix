<?php

declare(strict_types=1);

/**
 * Ejecuta {@see ApptablesUpdate} (POST: id_app, esquema, accion).
 */

use src\devel_db_admin\application\ApptablesUpdate;
use src\shared\web\ContestarJson;


try {
    $result = (new ApptablesUpdate())->ejecutar($_POST);
} catch (\Throwable $e) {
    ContestarJson::enviar($e->getMessage(), 'none', 200);
    return;
}

ContestarJson::enviar('', $result);
