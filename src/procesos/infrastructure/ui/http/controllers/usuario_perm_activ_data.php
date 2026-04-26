<?php

use src\procesos\application\UsuarioPermActivData;
use frontend\shared\web\ContestarJson;

ContestarJson::enviar('', UsuarioPermActivData::execute($_POST));
