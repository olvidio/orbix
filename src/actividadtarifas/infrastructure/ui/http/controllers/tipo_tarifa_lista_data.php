<?php
/**
 * Endpoint backend: listado del catalogo de tipos de tarifa.
 */

use src\actividadtarifas\application\TipoTarifaListaData;
use frontend\shared\web\ContestarJson;

$data = TipoTarifaListaData::execute();
ContestarJson::enviar('', $data);
