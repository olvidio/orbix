<?php

use src\inventario\application\TipoDocOpcionesData;
use src\shared\web\ContestarJson;

$data = TipoDocOpcionesData::build();

ContestarJson::enviar('', $data);
