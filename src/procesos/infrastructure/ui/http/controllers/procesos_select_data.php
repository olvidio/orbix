<?php

use src\procesos\application\ProcesosSelectData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', ProcesosSelectData::execute());
