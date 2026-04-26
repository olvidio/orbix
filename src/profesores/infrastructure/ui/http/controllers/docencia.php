<?php

use src\profesores\application\DocenciaLista;
use frontend\shared\web\ContestarJson;

ContestarJson::enviar('', DocenciaLista::getTablaData());
