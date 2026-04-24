<?php

use src\notas\application\PosiblesPreceptoresData;
use web\ContestarJson;

/**
 * Devuelve el desplegable de posibles preceptores (profesores STGR) con
 * el contrato estandar de refactor.md.
 *
 * Consumido por `fnjs_cmb_preceptor` en:
 * - `frontend/notas/view/form_notas_de_una_persona.phtml`
 * - `apps/actividadestudios/view/form_1303.phtml`
 */
$aOpciones = PosiblesPreceptoresData::execute();

$payload = [
    'id' => 'id_preceptor',
    'opciones' => $aOpciones,
    'blanco' => true,
    'val_blanco' => '',
    'selected' => '',
];

ContestarJson::enviar('', $payload);
