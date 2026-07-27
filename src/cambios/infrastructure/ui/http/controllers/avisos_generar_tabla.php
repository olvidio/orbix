<?php

/**
 * Endpoint HTTP: generar tabla de avisos de cambios.
 *
 * Delega al driver compartido CLI/web en
 * `src/cambios/infrastructure/cli/avisos_generar_tabla.php`
 * (detecta PHP_SAPI y responde ContestarJson en web).
 */

require __DIR__ . '/../../../cli/avisos_generar_tabla.php';
