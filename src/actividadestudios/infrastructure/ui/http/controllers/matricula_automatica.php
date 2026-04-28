<?php

use src\actividadestudios\application\MatriculaAutomatica;
use frontend\shared\web\ContestarJson;

/**
 * Matricula masivamente a una o varias personas en las asignaturas del plan
 * de estudios de su actividad vigente. Responde JSON.
 *
 * Sucesor de `apps/actividadestudios/controller/matricular.php`.
 */
$msg = MatriculaAutomatica::execute($_POST);

ContestarJson::enviar('', ['msg' => $msg]);
