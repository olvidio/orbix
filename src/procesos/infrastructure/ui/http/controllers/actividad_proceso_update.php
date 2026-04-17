<?php

use src\procesos\application\ActividadProcesoUpdate;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new ActividadProcesoUpdate();
echo $useCase->execute($_POST);
