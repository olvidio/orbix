<?php

use src\procesos\application\TipoActivProcesoLista;
use web\ContestarJson;

ContestarJson::enviar('', TipoActivProcesoLista::execute());
