<?php

use src\notas\application\NotaPersonaFormData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

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
 * (campos del PersonaNota + desplegables + helpers de opcionales genericas),
 * más `aOpcionesSituacion`, `lista_situacion_no_acta` y `vo` (constantes
 * NotaSituacion/TipoActa/NotaEpoca desde el dominio).
 */
$input = [
    'id_pau' => (int)FilterPostGet::post('id_pau'),
    'id_asignatura_real' => (string)FilterPostGet::post('id_asignatura_real'),
    'sel' => (array)FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'pau' => (string)FilterPostGet::post('pau'),
    'mod' => (string)FilterPostGet::post('mod'),
];

$data = (DependencyResolver::get(NotaPersonaFormData::class))->execute($input);
$data['helpers'] = (DependencyResolver::get(NotaPersonaFormData::class))->opcionalesGenericasHelpers();
$data = array_merge($data, (DependencyResolver::get(NotaPersonaFormData::class))->formNotasVoPack());

ContestarJson::enviar('', $data);
