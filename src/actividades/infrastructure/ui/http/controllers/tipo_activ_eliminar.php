<?php

use src\actividades\application\TipoActivEliminar;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new TipoActivEliminar();
echo $useCase->execute($_POST);
