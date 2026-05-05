<?php

declare(strict_types=1);

/**
 * Entrada legada; la subida de PDF del acta vive en `acta_pdf_subir.php` (src).
 */
$orbixRoot = dirname(__DIR__, 3);
require_once $orbixRoot . '/frontend/shared/global_header_front.inc';

require $orbixRoot . '/src/notas/infrastructure/ui/http/controllers/acta_pdf_subir.php';
