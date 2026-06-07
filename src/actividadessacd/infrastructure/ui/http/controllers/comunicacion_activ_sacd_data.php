<?php
/**
 * Endpoint backend: construye el listado de atencion de actividades a
 * comunicar a los sacd.
 */

use src\actividadessacd\application\ComunicacionActividadesSacdData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var ComunicacionActividadesSacdData $useCase */
$useCase = DependencyResolver::get(ComunicacionActividadesSacdData::class);
ContestarJson::enviar('', $useCase->execute($_POST));
