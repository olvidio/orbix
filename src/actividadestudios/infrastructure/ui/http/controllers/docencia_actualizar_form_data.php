<?php

use src\actividadestudios\application\DocenciaActualizarFormData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', (new DocenciaActualizarFormData())->execute());
