<?php

use src\actividadescentro\application\CentroEncargadoEliminar;
use src\actividadescentro\application\CentroEncargadoMutationCtx;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Actividad y centro salen de la cápsula `ctx` (la firman los datos de la
 * pantalla de centros encargados). Responde JSON `{success, mensaje, data}`.
 */
$input = CentroEncargadoMutationCtx::abrir($_POST, CentroEncargadoMutationCtx::ELIMINAR);
if ($input === null) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
/** @var CentroEncargadoEliminar $useCase */
$useCase = DependencyResolver::get(CentroEncargadoEliminar::class);
$error_txt = $useCase->execute($input);
ContestarJson::enviar($error_txt, 'ok');
