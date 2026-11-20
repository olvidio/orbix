<?php

declare(strict_types=1);

/**
 * Espejo web de la reconciliación de `cd_cargos_activ_dl`.
 *
 * POST `aplicar=1` escribe los cambios; sin ese parámetro sólo devuelve el
 * informe. POST `esquema` limita la ejecución a un esquema de comun.
 *
 * La lógica y las guardas viven en el driver compartido con el cron.
 */

// Herramienta sobre todos los esquemas: solo quien ve las herramientas de base de datos en el menú.
if (!\src\devel_db_admin\infrastructure\ui\http\PermisoHerramientasBdGuard::permiteEsquemas()) {
    \src\shared\web\ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

defined('ORBIX_CLI_DESDE_CONTROLADOR') || define('ORBIX_CLI_DESDE_CONTROLADOR', true);
require __DIR__ . '/../../../cli/cargos_activ_resincronizar.php';
