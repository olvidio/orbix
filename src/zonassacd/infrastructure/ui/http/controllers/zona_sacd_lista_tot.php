<?php

use src\zonassacd\application\ZonaSacdListaTot;
use frontend\shared\web\ContestarJson;

ContestarJson::enviar('', ZonaSacdListaTot::execute());
