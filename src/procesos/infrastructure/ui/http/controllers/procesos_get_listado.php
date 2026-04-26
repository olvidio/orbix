<?php

use src\procesos\application\ProcesosGetListado;
use frontend\shared\web\ContestarJson;

$useCase = new ProcesosGetListado();
ContestarJson::enviar('', $useCase->execute($_POST));
