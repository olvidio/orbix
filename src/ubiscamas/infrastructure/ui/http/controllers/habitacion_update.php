<?php

/**
 * Guardar una habitación de una casa (alta o modificación) y sus camas.
 * La habitación y la casa salen de la cápsula `ctx_update`; el resto, del formulario.
 */

use src\shared\domain\helpers\FuncTablasSupport as F;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\ubiscamas\application\HabitacionGuardar;

try {
    $ctx = HashB::open((string) \src\shared\domain\helpers\FilterPostGet::post('ctx_update'), 'habitacion_update');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$error_txt = DependencyResolver::get(HabitacionGuardar::class)->execute([
    'id_habitacion' => F::inputString($ctx, 'id_habitacion'),
    'id_ubi' => F::inputInt($ctx, 'id_ubi'),
    'orden' => F::inputInt($_POST, 'orden'),
    'nombre' => F::inputString($_POST, 'nombre'),
    'numero_camas' => F::inputInt($_POST, 'numero_camas'),
    'numero_camas_vip' => F::inputInt($_POST, 'numero_camas_vip'),
    'planta' => F::inputString($_POST, 'planta'),
    'tipoLavabo' => F::inputInt($_POST, 'tipoLavabo'),
    'sillon' => F::isTrue(F::inputString($_POST, 'sillon')) === true,
    'adaptada' => F::isTrue(F::inputString($_POST, 'adaptada')) === true,
    'observaciones' => F::inputString($_POST, 'observaciones'),
    'despacho' => F::isTrue(F::inputString($_POST, 'despacho')) === true,
    'new_camas_desc' => F::inputStringList($_POST, 'new_camas_desc'),
    'new_camas_larga' => F::inputStringList($_POST, 'new_camas_larga'),
    'new_camas_vip' => F::inputStringList($_POST, 'new_camas_vip'),
]);

ContestarJson::enviar($error_txt, 'ok');
