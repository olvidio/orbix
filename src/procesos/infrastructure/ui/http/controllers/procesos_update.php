<?php

use src\procesos\application\ProcesosUpdate;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new ProcesosUpdate();
echo $useCase->execute($_POST);
