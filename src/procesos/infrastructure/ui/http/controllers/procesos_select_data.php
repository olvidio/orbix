<?php

use src\procesos\application\ProcesosSelectData;
use frontend\shared\web\ContestarJson;

ContestarJson::enviar('', ProcesosSelectData::execute());
