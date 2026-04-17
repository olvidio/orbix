<?php

use src\procesos\application\ProcesosRegenerar;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new ProcesosRegenerar();
echo $useCase->execute($_POST);
