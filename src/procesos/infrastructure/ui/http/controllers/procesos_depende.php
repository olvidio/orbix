<?php

use src\procesos\application\ProcesosDepende;
use src\shared\web\ContestarJson;

$useCase = new ProcesosDepende();
ContestarJson::enviar('', $useCase->execute($_POST));
