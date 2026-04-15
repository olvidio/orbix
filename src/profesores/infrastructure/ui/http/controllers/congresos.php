<?php

use src\profesores\application\CongresosLista;
use web\ContestarJson;

$jsondata = ContestarJson::respuestaPhp('', CongresosLista::getTablaData());
ContestarJson::send($jsondata);
