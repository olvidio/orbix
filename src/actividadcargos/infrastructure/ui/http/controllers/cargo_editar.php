<?php

use src\actividadcargos\application\ActividadCargoEditar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Edita un `ActividadCargo` existente. Responde JSON `{success, mensaje, data}`.
 *
 * El frontend debe enviar `asis_presente=1` cuando el form incluye el input
 * `asis` (mantiene la semantica `isset($_POST['asis'])` del legacy).
 */
$input = $_POST;
$input['asis_presente'] = isset($_POST['asis_presente']) && $_POST['asis_presente'] !== ''
    ? '1'
    : (isset($_POST['asis']) ? '1' : '');

/** @var ActividadCargoEditar $useCase */
$useCase = DependencyResolver::get(ActividadCargoEditar::class);
$error_txt = $useCase->execute($input);
ContestarJson::enviar($error_txt, 'ok');
