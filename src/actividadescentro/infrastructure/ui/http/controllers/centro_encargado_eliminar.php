<?php
/**
 * Endpoint backend: elimina un CentroEncargado de una actividad.
 * Responde JSON `{success, mensaje, data}` via ContestarJson::enviar.
 */

use src\actividadescentro\application\CentroEncargadoEliminar;
use src\shared\web\ContestarJson;

$error_txt = CentroEncargadoEliminar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
