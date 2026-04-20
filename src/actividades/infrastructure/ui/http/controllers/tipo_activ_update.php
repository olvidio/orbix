<?php

use src\actividades\application\TipoActivUpdate;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new TipoActivUpdate();
echo $useCase->execute($_POST);
