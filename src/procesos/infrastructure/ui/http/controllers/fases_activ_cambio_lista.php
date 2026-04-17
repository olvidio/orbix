<?php

use src\procesos\application\FasesActivCambioLista;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new FasesActivCambioLista();
echo $useCase->execute($_POST);
