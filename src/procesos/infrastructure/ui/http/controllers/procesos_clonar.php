<?php

use src\procesos\application\ProcesosClonar;
use frontend\shared\web\ContestarJson;

$useCase = new ProcesosClonar();
$error = $useCase->execute($_POST);
ContestarJson::enviar($error);
