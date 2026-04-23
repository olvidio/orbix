<?php
/**
 * Endpoint backend: construye el listado de atencion de actividades a
 * comunicar a los sacd (incluidas las de los "sacd de paso" cuando
 * procede). Responde JSON `{success, mensaje, data}` via
 * `ContestarJson::enviar`.
 */

use src\actividadessacd\application\ComunicacionActividadesSacdData;
use web\ContestarJson;

$data = ComunicacionActividadesSacdData::execute($_POST);
ContestarJson::enviar('', $data);
