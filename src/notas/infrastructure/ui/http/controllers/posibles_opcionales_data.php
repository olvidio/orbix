<?php

use src\notas\application\PosiblesOpcionalesData;
use web\ContestarJson;

/**
 * Devuelve las asignaturas opcionales que puede cursar la persona con el
 * contrato estandar de desplegable (ver `refactor.md` §"Desplegables
 * devueltos por endpoints AJAX: payload + constructor en frontend").
 *
 * Consumido por `fnjs_cmb_opcional` en:
 * - `frontend/notas/view/form_1011.phtml`
 * - `apps/actividadestudios/view/form_1303.phtml`
 */
$aOpciones = PosiblesOpcionalesData::execute($_POST);

$payload = [
    'id' => 'id_asignatura',
    'opciones' => $aOpciones,
    'blanco' => true,
    'val_blanco' => '',
    'selected' => '',
];

ContestarJson::enviar('', $payload);
