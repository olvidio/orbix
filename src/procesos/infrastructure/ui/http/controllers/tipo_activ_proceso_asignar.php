<?php

use src\procesos\application\TipoActivProcesoAsignar;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new TipoActivProcesoAsignar();
echo $useCase->execute($_POST);
