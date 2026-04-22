<?php

use src\procesos\application\ActividadProcesoUpdate;
use web\ContestarJson;

$useCase = new ActividadProcesoUpdate();
$error = $useCase->execute($_POST);
ContestarJson::enviar($error);
