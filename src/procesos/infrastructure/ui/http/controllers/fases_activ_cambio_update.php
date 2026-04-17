<?php

use src\procesos\application\FasesActivCambioUpdate;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new FasesActivCambioUpdate();
echo $useCase->execute($_POST);
