<?php

use src\profesores\application\DocenciaLista;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', DocenciaLista::getTablaData());
