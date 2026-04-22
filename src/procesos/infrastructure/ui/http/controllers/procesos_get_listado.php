<?php

use src\procesos\application\ProcesosGetListado;
use web\ContestarJson;

$useCase = new ProcesosGetListado();
ContestarJson::enviar('', $useCase->execute($_POST));
