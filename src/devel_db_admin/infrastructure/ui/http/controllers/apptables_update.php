<?php

declare(strict_types=1);

/**
 * Ejecuta {@see ApptablesUpdate} (POST: id_app, esquema, accion).
 */

use src\devel_db_admin\application\ApptablesUpdate;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;


try {
    HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_apptables'),
        'apptables_update'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

try {
    $result = (new ApptablesUpdate())->ejecutar($_POST);
} catch (\Throwable $e) {
    ContestarJson::enviar($e->getMessage(), 'none', 200);
    return;
}

ContestarJson::enviar('', $result);
