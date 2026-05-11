<?php

use src\procesos\application\ProcesosRegenerar;
use src\shared\web\ContestarJson;

$useCase = new ProcesosRegenerar();
$error = $useCase->execute($_POST);
ContestarJson::enviar($error);
