<?php
/**
 * Endpoint backend: asigna un CentroEncargado a una actividad.
 * Responde JSON `{success, mensaje, data}` via ContestarJson::enviar.
 */

use src\actividadescentro\application\CentroEncargadoAsignar;
use src\shared\web\ContestarJson;

$error_txt = CentroEncargadoAsignar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
