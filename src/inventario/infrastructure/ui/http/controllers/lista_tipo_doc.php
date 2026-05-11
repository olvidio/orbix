<?php

use src\inventario\application\TipoDocOpcionesData;
use frontend\shared\web\ContestarJson;

$data = TipoDocOpcionesData::build();

ContestarJson::enviar('', $data);
