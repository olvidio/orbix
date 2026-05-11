<?php

use src\misas\application\BuscarPlanSacdData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', BuscarPlanSacdData::getData());
