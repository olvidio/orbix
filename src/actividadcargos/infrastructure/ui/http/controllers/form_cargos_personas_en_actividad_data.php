<?php

use src\actividadcargos\application\FormCargosPersonasEnActividadData;
use frontend\shared\web\ContestarJson;

$result = FormCargosPersonasEnActividadData::build($_POST);
$error = (string)($result['error'] ?? '');
unset($result['error']);
ContestarJson::enviar($error, $result);
