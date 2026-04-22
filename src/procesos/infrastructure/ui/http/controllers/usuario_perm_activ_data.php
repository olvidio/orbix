<?php

use src\procesos\application\UsuarioPermActivData;
use web\ContestarJson;

ContestarJson::enviar('', UsuarioPermActivData::execute($_POST));
