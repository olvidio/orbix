<?php

use src\notas\application\BuscarActaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Busca un acta por su numero abreviado. Consumido por
 * `frontend/notas/view/form_notas_de_una_persona.phtml` (`fnjs_buscar_acta`).
 *
 * Respuesta: `ContestarJson` con `data` = payload JSON devuelto por
 * `BuscarActaData::execute`. El JS decodifica `data` con guardia
 * `(typeof json.data === 'string') ? JSON.parse(json.data) : json.data`.
 */
$data = (DependencyResolver::get(BuscarActaData::class))->execute($_POST);
ContestarJson::enviar('', $data);
