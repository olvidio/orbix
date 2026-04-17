<?php

use src\procesos\application\ProcesosGet;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new ProcesosGet();
echo $useCase->execute($_POST);
