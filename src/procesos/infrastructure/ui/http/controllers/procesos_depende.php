<?php

use src\procesos\application\ProcesosDepende;
use frontend\shared\web\ContestarJson;

$useCase = new ProcesosDepende();
ContestarJson::enviar('', $useCase->execute($_POST));
