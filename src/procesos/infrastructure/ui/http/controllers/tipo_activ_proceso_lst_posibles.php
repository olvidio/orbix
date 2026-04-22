<?php

use src\procesos\application\TipoActivProcesoLstPosibles;
use web\ContestarJson;

$useCase = new TipoActivProcesoLstPosibles();
ContestarJson::enviar('', $useCase->execute($_POST));
