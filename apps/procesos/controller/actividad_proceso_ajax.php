<?php
/**
 * Compatibilidad: URL historica bajo apps/. Preferir
 * /src/procesos/actividad_proceso_ajax (dispatcher multi-`que`, marcado como
 * DEPRECADO) para nuevos usos.
 */
require __DIR__ . '/../../../src/procesos/infrastructure/ui/http/controllers/actividad_proceso_ajax.php';
