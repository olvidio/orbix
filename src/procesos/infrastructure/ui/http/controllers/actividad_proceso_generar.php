<?php

use src\procesos\application\ActividadProcesoGenerar;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new ActividadProcesoGenerar();
echo $useCase->execute($_POST);
