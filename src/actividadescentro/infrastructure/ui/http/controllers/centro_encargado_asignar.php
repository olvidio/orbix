<?php

use src\actividadescentro\application\CentroEncargadoAsignar;
use src\actividadescentro\application\CentroEncargadoMutationCtx;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Actividad y centro salen de la cápsula `ctx` (la firman los datos de la
 * pantalla de centros encargados). Responde JSON `{success, mensaje, data}`.
 */
$input = CentroEncargadoMutationCtx::abrir($_POST, CentroEncargadoMutationCtx::ASIGNAR);
if ($input === null) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
/** @var CentroEncargadoAsignar $useCase */
$useCase = DependencyResolver::get(CentroEncargadoAsignar::class);
$error_txt = $useCase->execute($input);
ContestarJson::enviar($error_txt, 'ok');
