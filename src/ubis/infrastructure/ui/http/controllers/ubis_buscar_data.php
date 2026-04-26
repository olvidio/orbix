<?php

use src\ubis\application\UbisBuscarOpcionesData;
use frontend\shared\web\ContestarJson;

ContestarJson::enviar('', UbisBuscarOpcionesData::execute());
