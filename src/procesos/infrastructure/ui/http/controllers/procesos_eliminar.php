<?php

use src\procesos\application\ProcesosEliminar;
use src\shared\web\ContestarJson;

$useCase = new ProcesosEliminar();
$error = $useCase->execute($_POST);
ContestarJson::enviar($error);
