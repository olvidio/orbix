<?php
/**
 * Compatibilidad: URL historica bajo apps/. Preferir
 * /src/procesos/fases_activ_cambio_ajax (dispatcher multi-`que`, marcado como
 * DEPRECADO) para nuevos usos.
 */
require __DIR__ . '/../../../src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_ajax.php';
