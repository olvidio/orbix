<?php
/**
 * Endpoint backend: listado de `GrupoCasa` (relaciones padre ↔ hijo).
 */

use src\casas\application\GrupoCasaListaData;
use src\shared\web\ContestarJson;

$data = GrupoCasaListaData::execute();
ContestarJson::enviar('', $data);
