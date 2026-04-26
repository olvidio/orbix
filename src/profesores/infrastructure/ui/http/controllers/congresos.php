<?php

use src\profesores\application\CongresosLista;
use frontend\shared\web\ContestarJson;

ContestarJson::enviar('', CongresosLista::getTablaData());
