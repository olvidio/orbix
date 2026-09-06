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

require __DIR__ . '/../../../cli/centros_resincronizar.php';
