<?php

use src\procesos\application\ProcesosClonar;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new ProcesosClonar();
echo $useCase->execute($_POST);
