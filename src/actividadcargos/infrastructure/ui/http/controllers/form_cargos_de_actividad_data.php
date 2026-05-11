<?php

use src\actividadcargos\application\FormCargosDeActividadData;
use src\shared\web\ContestarJson;

$result = FormCargosDeActividadData::build($_POST);
$error = (string)($result['error'] ?? '');
unset($result['error']);
ContestarJson::enviar($error, $result);
