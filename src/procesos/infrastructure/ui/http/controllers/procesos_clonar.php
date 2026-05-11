<?php

use src\procesos\application\ProcesosClonar;
use src\shared\web\ContestarJson;

$useCase = new ProcesosClonar();
$error = $useCase->execute($_POST);
ContestarJson::enviar($error);
