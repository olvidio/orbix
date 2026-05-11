<?php

use src\procesos\application\UsuarioPermActivData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', UsuarioPermActivData::execute($_POST));
