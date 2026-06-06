<?php
/**
 * Endpoint backend: asigna un CentroEncargado a una actividad.
 * Responde JSON `{success, mensaje, data}` via ContestarJson::enviar.
 */

use src\actividadescentro\application\CentroEncargadoAsignar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CentroEncargadoAsignar $useCase */
$useCase = DependencyResolver::get(CentroEncargadoAsignar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
