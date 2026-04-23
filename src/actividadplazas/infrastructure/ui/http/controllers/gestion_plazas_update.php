<?php
/**
 * Endpoint backend: actualiza las plazas (totales, concedidas o
 * pedidas) desde la edicion inline de `web\TablaEditable`. Responde
 * JSON `{success, mensaje, data}` via `web\ContestarJson::enviar`
 * (contrato estandar del resto de endpoints de `src/`).
 */

use src\actividadplazas\application\GestionPlazasUpdate;
use web\ContestarJson;

$error = GestionPlazasUpdate::execute($_POST);
ContestarJson::enviar($error, 'ok');
