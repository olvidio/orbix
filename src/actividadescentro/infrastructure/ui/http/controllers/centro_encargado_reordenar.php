<?php
/**
 * Endpoint backend: reordena un CentroEncargado (mas / menos prioridad).
 * Responde JSON `{success, mensaje, data}` via ContestarJson::enviar.
 */

use src\actividadescentro\application\CentroEncargadoReordenar;
use frontend\shared\web\ContestarJson;

$error_txt = CentroEncargadoReordenar::execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
