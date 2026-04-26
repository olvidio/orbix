<?php

use src\procesos\application\ProcesosGet;
use frontend\shared\web\ContestarJson;

$useCase = new ProcesosGet();
ContestarJson::enviar('', $useCase->execute($_POST));
