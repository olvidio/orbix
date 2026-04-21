<?php

/**
 * @deprecated Usar `/src/encargossacd/zonas_get_select_data`. Este fichero solo reenvía por compatibilidad.
 */

require_once __DIR__ . '/../../shared/global_header_front.inc';

$root = dirname(__DIR__, 3);
require $root . '/src/encargossacd/infrastructure/ui/http/controllers/zonas_get_select_data.php';
