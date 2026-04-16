<?php

use src\profesores\application\CongresosLista;
use web\ContestarJson;

ContestarJson::enviar('', CongresosLista::getTablaData());
