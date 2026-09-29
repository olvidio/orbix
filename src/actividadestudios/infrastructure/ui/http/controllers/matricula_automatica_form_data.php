<?php

use src\actividadestudios\application\MatriculaAutomaticaFormData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', (new MatriculaAutomaticaFormData())->execute());
