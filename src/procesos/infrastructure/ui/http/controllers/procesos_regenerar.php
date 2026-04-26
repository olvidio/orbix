<?php

use src\procesos\application\ProcesosRegenerar;
use frontend\shared\web\ContestarJson;

$useCase = new ProcesosRegenerar();
$error = $useCase->execute($_POST);
ContestarJson::enviar($error);
