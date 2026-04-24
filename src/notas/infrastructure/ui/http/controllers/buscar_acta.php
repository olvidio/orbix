<?php

use src\notas\application\BuscarActaData;
use web\ContestarJson;

/**
 * Busca un acta por su numero abreviado. Consumido por
 * `frontend/notas/view/form_notas_de_una_persona.phtml` (`fnjs_buscar_acta`).
 *
 * Respuesta: `ContestarJson` con `data` = payload JSON devuelto por
 * `BuscarActaData::execute`. El JS decodifica `data` con guardia
 * `(typeof json.data === 'string') ? JSON.parse(json.data) : json.data`.
 */
$data = BuscarActaData::execute($_POST);
ContestarJson::enviar('', $data);
