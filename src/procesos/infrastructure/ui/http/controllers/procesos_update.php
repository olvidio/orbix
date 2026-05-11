<?php

use src\procesos\application\ProcesosUpdate;
use src\shared\web\ContestarJson;

$useCase = new ProcesosUpdate();
$error = $useCase->execute($_POST);
ContestarJson::enviar($error);
