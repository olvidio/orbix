<?php
/**
 * Endpoint backend: elimina un CentroEncargado de una actividad.
 * Responde JSON `{success, mensaje, data}` via ContestarJson::enviar.
 */

use src\actividadescentro\application\CentroEncargadoEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CentroEncargadoEliminar $useCase */
$useCase = DependencyResolver::get(CentroEncargadoEliminar::class);
$error_txt = $useCase->execute($_POST);
ContestarJson::enviar($error_txt, 'ok');
