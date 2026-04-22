<?php

use src\notas\application\ActaPdfSubir;
use web\ContestarJson;

/**
 * @deprecated Usar `src/notas/infrastructure/ui/http/controllers/acta_pdf_subir.php`.
 * Shim de compatibilidad para `acta_ver.phtml` hasta la migracion completa
 * de vistas de actas.
 */
require_once 'apps/core/global_header.inc';
require_once 'apps/core/global_object.inc';

$error_txt = ActaPdfSubir::execute($_POST, $_FILES);
ContestarJson::enviar($error_txt, 'ok');
