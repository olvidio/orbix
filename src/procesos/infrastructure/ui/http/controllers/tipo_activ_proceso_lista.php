<?php

use src\procesos\application\TipoActivProcesoLista;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new TipoActivProcesoLista();
echo $useCase->execute($_POST);
