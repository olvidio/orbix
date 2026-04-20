<?php

use src\actividades\application\TipoActivFormModificar;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new TipoActivFormModificar();
echo $useCase->execute($_POST);
