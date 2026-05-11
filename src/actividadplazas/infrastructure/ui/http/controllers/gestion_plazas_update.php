<?php
/**
 * Endpoint backend: actualiza las plazas (totales, concedidas o
 * pedidas) desde la edicion inline de `frontend\shared\web\TablaEditable`. Responde
 * JSON `{success, mensaje, data}` via `src\shared\web\ContestarJson::enviar`
 * (contrato estandar del resto de endpoints de `src/`).
 */

use src\actividadplazas\application\GestionPlazasUpdate;
use src\shared\web\ContestarJson;

$error = GestionPlazasUpdate::execute($_POST);
ContestarJson::enviar($error, 'ok');
