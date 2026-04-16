<?php

use src\profesores\application\DocenciaLista;
use web\ContestarJson;

ContestarJson::enviar('', DocenciaLista::getTablaData());
