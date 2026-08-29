<?php

declare(strict_types=1);

/**
 * Espejo web de la reconciliación de `cp_sacd`.
 *
 * POST `aplicar=1` escribe los cambios; sin ese parámetro sólo devuelve el
 * informe. POST `esquema` limita la ejecución a un esquema de comun.
 *
 * La lógica y las guardas viven en el driver compartido con el cron.
 */

require __DIR__ . '/../../../cli/sacd_resincronizar.php';
