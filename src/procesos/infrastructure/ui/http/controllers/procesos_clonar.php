<?php

use src\procesos\application\ProcesosClonar;
use web\ContestarJson;

$useCase = new ProcesosClonar();
$error = $useCase->execute($_POST);
ContestarJson::enviar($error);
