<?php

declare(strict_types=1);

use frontend\shared\FrontBootstrap;

/**
 * Eliminación AJAX del PDF firmado del acta.
 *
 * La lógica vive en `src/notas/infrastructure/ui/http/controllers/acta_pdf_eliminar.php`.
 */
$orbixRoot = dirname(__DIR__, 3);
require_once $orbixRoot . '/frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
require $orbixRoot . '/src/notas/infrastructure/ui/http/controllers/acta_pdf_eliminar.php';
