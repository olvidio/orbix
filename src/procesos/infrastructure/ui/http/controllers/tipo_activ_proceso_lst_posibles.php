<?php

use src\procesos\application\TipoActivProcesoLstPosibles;
use frontend\shared\web\ContestarJson;

$useCase = new TipoActivProcesoLstPosibles();
ContestarJson::enviar('', $useCase->execute($_POST));
