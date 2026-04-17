<?php

/**
 * Compatibilidad: URL historica bajo apps/ para el dispatcher AJAX
 * multi-`que` de procesos. Preferir la ruta /src/procesos/procesos_ajax
 * para llamadas nuevas.
 */
require __DIR__ . '/../../../src/procesos/infrastructure/ui/http/controllers/procesos_ajax.php';
