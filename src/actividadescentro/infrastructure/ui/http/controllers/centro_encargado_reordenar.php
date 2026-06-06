<?php
/**
 * Endpoint backend: reordena un CentroEncargado (mas / menos prioridad).
 * Responde JSON `{success, mensaje, data}` via ContestarJson::enviar.
 */

use src\actividadescentro\application\CentroEncargadoReordenar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CentroEncargadoReordenar $useCase */
$useCase = DependencyResolver::get(CentroEncargadoReordenar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
