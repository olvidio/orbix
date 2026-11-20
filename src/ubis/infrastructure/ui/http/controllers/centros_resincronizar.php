<?php

declare(strict_types=1);

/**
 * Espejo web de la reconciliación de las copias de centros.
 *
 * POST `aplicar=1` escribe los cambios; sin ese parámetro sólo devuelve el
 * informe. POST `esquema` limita la ejecución a un esquema de comun.
 *
 * La copia que se reconcilia depende de la instalación (sv o sf); la lógica y
 * las guardas viven en el driver compartido con el cron.
 */

use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

// Herramienta sobre todos los esquemas: solo quien ve las herramientas de base de datos en el menú.
if (!\src\devel_db_admin\infrastructure\ui\http\PermisoHerramientasBdGuard::permiteEsquemas()) {
    \src\shared\web\ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

try {
    HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_resincronizar'),
        'centros_resincronizar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

defined('ORBIX_CLI_DESDE_CONTROLADOR') || define('ORBIX_CLI_DESDE_CONTROLADOR', true);
require __DIR__ . '/../../../cli/centros_resincronizar.php';
