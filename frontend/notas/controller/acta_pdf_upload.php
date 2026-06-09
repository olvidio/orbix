<?php

use frontend\shared\FrontBootstrap;

declare(strict_types=1);

/**
 * Subida AJAX del PDF firmado del acta (multipart / FormData).
 *
 * La lógica vive en `src/notas/infrastructure/ui/http/controllers/acta_pdf_subir.php`.
 */
$orbixRoot = dirname(__DIR__, 3);
require_once $orbixRoot . '/frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
require $orbixRoot . '/src/notas/infrastructure/ui/http/controllers/acta_pdf_subir.php';
