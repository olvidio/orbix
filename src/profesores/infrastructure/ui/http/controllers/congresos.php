<?php

use src\profesores\application\CongresosLista;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', CongresosLista::getTablaData());
