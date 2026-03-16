<?php

use src\usuarios\application\rolesLista;
use web\ContestarJson;

$jsondata = rolesLista::rolesLista();

// envía una Response
ContestarJson::send($jsondata);
