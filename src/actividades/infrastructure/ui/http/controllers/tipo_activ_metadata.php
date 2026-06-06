<?php
/**
 * Endpoint backend que devuelve, en una sola respuesta JSON, los datos que
 * necesita {@see \frontend\actividades\helpers\TiposDeActividades} para
 * funcionar sin tocar el repositorio:
 *
 *  - `maps`: los 4 mapas estáticos texto→código del id_tipo_activ.
 *  - `filas`: lista plana `{id_tipo_activ, nombre}` de `a_tipos_actividad`.
 *
 * Sustituye al antiguo `tipo_activ_filas`, que solo devolvía las filas y
 * obligaba al frontend a duplicar los maps.
 */

use src\shared\web\ContestarJson;
use src\shared\infrastructure\DependencyResolver;
use src\actividades\application\TipoActivMetadata;

$payload = DependencyResolver::get(TipoActivMetadata::class)->execute();
ContestarJson::enviar('', $payload);
