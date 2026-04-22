<?php

use src\notas\application\TesseraCopiar;
use web\ContestarJson;

/**
 * @deprecated Usar `src/notas/infrastructure/ui/http/controllers/tessera_copiar.php`.
 * Shim de compatibilidad para `tessera_copiar_select.html.twig` hasta la
 * migracion a `frontend/notas/view/tessera_copiar_select.phtml`.
 */
require_once 'apps/core/global_header.inc';
require_once 'apps/core/global_object.inc';

$error_txt = TesseraCopiar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
