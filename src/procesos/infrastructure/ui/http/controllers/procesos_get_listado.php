<?php

use src\procesos\application\ProcesosGetListado;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new ProcesosGetListado();
echo $useCase->execute($_POST);
