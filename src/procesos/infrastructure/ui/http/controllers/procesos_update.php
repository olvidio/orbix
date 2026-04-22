<?php

use src\procesos\application\ProcesosUpdate;
use web\ContestarJson;

$useCase = new ProcesosUpdate();
$error = $useCase->execute($_POST);
ContestarJson::enviar($error);
