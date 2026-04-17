<?php

use src\procesos\application\FasesActivCambioGet;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new FasesActivCambioGet();
echo $useCase->execute($_POST);
