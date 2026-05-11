<?php

use src\procesos\application\TipoActivProcesoLstPosibles;
use src\shared\web\ContestarJson;

$useCase = new TipoActivProcesoLstPosibles();
ContestarJson::enviar('', $useCase->execute($_POST));
