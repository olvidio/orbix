<?php

use src\procesos\application\ProcesosSelectData;
use web\ContestarJson;

ContestarJson::enviar('', ProcesosSelectData::execute());
