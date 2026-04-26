<?php

use src\usuarios\application\rolesLista;
use frontend\shared\web\ContestarJson;

$jsondata = rolesLista::rolesLista();

// envía una Response
ContestarJson::send($jsondata);
