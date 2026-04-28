<?php

use frontend\shared\web\ContestarJson;
use src\procesos\application\FasesActivCambioTipoActividadHtmlData;

ContestarJson::enviar('', FasesActivCambioTipoActividadHtmlData::execute($_POST));
