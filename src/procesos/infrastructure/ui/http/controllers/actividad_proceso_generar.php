<?php

use src\procesos\application\ActividadProcesoGenerar;
use frontend\shared\web\ContestarJson;

$useCase = new ActividadProcesoGenerar();
$error = $useCase->execute($_POST);
ContestarJson::enviar($error);
