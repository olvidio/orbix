<?php

use src\inventario\application\ColeccionesOpcionesData;
use frontend\shared\web\ContestarJson;

$data = ColeccionesOpcionesData::build();

ContestarJson::enviar('', $data);
