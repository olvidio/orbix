<?php

use src\actividades\application\TipoActivNuevo;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new TipoActivNuevo();
echo $useCase->execute($_POST);
