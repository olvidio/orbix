<?php

use src\procesos\application\ProcesosEliminar;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new ProcesosEliminar();
echo $useCase->execute($_POST);
