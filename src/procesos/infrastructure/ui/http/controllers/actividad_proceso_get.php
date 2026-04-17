<?php

use src\procesos\application\ActividadProcesoGet;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new ActividadProcesoGet();
echo $useCase->execute($_POST);
