<?php

use src\procesos\application\TipoActivProcesoLista;
use frontend\shared\web\ContestarJson;

ContestarJson::enviar('', TipoActivProcesoLista::execute());
