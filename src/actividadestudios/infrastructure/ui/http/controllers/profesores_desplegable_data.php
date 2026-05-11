<?php

use src\actividadestudios\application\ProfesoresDesplegableData;
use src\shared\web\ContestarJson;

/**
 * Devuelve JSON con los datos para construir el desplegable de profesores.
 * Sucesor de `apps/actividadestudios/controller/lista_profesores_ajax.php`.
 */
$data = ProfesoresDesplegableData::execute($_POST);
ContestarJson::enviar('', 'ok', $data);
