<?php

/**
 * Endpoint HTTP: generar tabla de avisos de cambios.
 *
 * Delega al driver compartido CLI/web en
 * `src/cambios/infrastructure/cli/avisos_generar_tabla.php`
 * (detecta PHP_SAPI y responde ContestarJson en web).
 */

use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_generar_tabla'),
        'avisos_generar_tabla'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

require __DIR__ . '/../../../cli/avisos_generar_tabla.php';
