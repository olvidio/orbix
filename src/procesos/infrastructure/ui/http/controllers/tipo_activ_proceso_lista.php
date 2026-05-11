<?php

use src\procesos\application\TipoActivProcesoLista;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', TipoActivProcesoLista::execute());
