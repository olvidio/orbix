<?php

use src\ubis\application\UbisBuscarOpcionesData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', UbisBuscarOpcionesData::execute());
