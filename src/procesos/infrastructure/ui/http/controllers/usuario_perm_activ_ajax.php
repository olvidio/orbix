<?php

/*
 * Endpoint para refrescar las opciones del desplegable `fase_ref` en la
 * pantalla usuario_perm_activ segun el tipo de actividad seleccionado y
 * si se trata de la delegacion propia o no.
 *
 * Respuesta JSON estandar (refactor.md) con `data.opciones` como mapa
 * value => label. El frontend construye los `<option>` con el helper JS
 * `fnjs_construir_desplegable` (o equivalente).
 */

use src\procesos\application\UsuarioPermActivFases;
use frontend\shared\web\ContestarJson;

ContestarJson::enviar('', UsuarioPermActivFases::execute($_POST));
