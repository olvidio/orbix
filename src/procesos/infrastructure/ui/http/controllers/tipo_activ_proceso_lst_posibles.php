<?php

use src\procesos\application\TipoActivProcesoLstPosibles;

header('Content-Type: text/plain; charset=UTF-8');

$useCase = new TipoActivProcesoLstPosibles();
echo $useCase->execute($_POST);
