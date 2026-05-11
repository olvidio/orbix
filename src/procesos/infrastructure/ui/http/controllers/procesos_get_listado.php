<?php

use src\procesos\application\ProcesosGetListado;
use src\shared\web\ContestarJson;

$useCase = new ProcesosGetListado();
ContestarJson::enviar('', $useCase->execute($_POST));
