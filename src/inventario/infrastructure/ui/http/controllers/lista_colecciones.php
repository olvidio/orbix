<?php

use src\inventario\application\ColeccionesOpcionesData;
use src\shared\web\ContestarJson;

$data = ColeccionesOpcionesData::build();

ContestarJson::enviar('', $data);
