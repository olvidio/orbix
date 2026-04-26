<?php
/**
 * Endpoint backend: listado de relaciones tarifa ↔ tipo actividad.
 */

use src\actividadtarifas\application\RelacionTarifaListaData;
use frontend\shared\web\ContestarJson;

$data = RelacionTarifaListaData::execute();
ContestarJson::enviar('', $data);
