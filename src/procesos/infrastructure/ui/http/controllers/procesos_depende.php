<?php

use src\procesos\application\ProcesosDepende;
use web\ContestarJson;

$useCase = new ProcesosDepende();
ContestarJson::enviar('', $useCase->execute($_POST));
