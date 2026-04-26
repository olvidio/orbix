<?php

use src\misas\application\BuscarPlanSacdData;
use frontend\shared\web\ContestarJson;

ContestarJson::enviar('', BuscarPlanSacdData::getData());
