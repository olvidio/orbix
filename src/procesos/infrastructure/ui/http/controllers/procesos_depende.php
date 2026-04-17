<?php

use src\procesos\application\ProcesosDepende;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new ProcesosDepende();
echo $useCase->execute($_POST);
