<?php

use src\notas\application\NotaPersonaFormData;
use web\ContestarJson;

/**
 * Endpoint backend que prepara los datos para `form_notas_de_una_persona.phtml`
 * (alta/edicion de `PersonaNota`).
 *
 * Consumido por `frontend/notas/controller/form_notas_de_una_persona.php` via
 * `PostRequest::getDataFromUrl()` (ver refactor.md "Patron de llamada
 * backend desde frontend").
 *
 * Entrada (POST):
 *   - id_pau               int
 *   - id_asignatura_real   string (editar)
 *   - sel                  array  (checkbox origen)
 *   - pau                  string ('p' cuando viene de checkbox de persona)
 *   - mod                  string ('nuevo' | 'editar' | '')
 *
 * Salida: array con todas las claves necesarias para la vista
 * (campos del PersonaNota + desplegables + helpers de opcionales genericas).
 */
$input = [
    'id_pau' => (int)filter_input(INPUT_POST, 'id_pau'),
    'id_asignatura_real' => (string)filter_input(INPUT_POST, 'id_asignatura_real'),
    'sel' => (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'pau' => (string)filter_input(INPUT_POST, 'pau'),
    'mod' => (string)filter_input(INPUT_POST, 'mod'),
];

$data = NotaPersonaFormData::execute($input);
$data['helpers'] = NotaPersonaFormData::opcionalesGenericasHelpers();

ContestarJson::enviar('', $data);
