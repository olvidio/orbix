<?php

use src\profesores\application\DocenciaLista;
use web\ContestarJson;

$jsondata = ContestarJson::respuestaPhp('', DocenciaLista::getTablaData());
ContestarJson::send($jsondata);
