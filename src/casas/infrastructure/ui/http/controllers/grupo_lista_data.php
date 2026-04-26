<?php
/**
 * Endpoint backend: listado de `GrupoCasa` (relaciones padre ↔ hijo).
 */

use src\casas\application\GrupoCasaListaData;
use frontend\shared\web\ContestarJson;

$data = GrupoCasaListaData::execute();
ContestarJson::enviar('', $data);
