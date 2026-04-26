<?php

/**
 * Pantalla de menu "matricular a todos". Dispara `MatriculaAutomatica`
 * (matricula masiva del plan de estudios de cada persona en situacion A)
 * y muestra el resultado como mensaje.
 *
 * Sucesor de `apps/actividadestudios/controller/matricular.php`.
 */

use frontend\shared\model\ViewNewPhtml;
use src\actividadestudios\application\MatriculaAutomatica;

require_once("frontend/shared/global_header_front.inc");
require_once 'apps/core/global_object.inc';

$msg = MatriculaAutomatica::execute($_POST);

(new ViewNewPhtml('frontend\\actividadestudios\\controller'))
    ->renderizar('matricular.phtml', [
        'oPosicion' => $oPosicion,
        'msg' => $msg,
    ]);
