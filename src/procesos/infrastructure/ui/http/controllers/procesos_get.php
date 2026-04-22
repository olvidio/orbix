<?php

use src\procesos\application\ProcesosGet;
use web\ContestarJson;

$useCase = new ProcesosGet();
ContestarJson::enviar('', $useCase->execute($_POST));
