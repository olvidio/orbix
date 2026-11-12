<?php

use src\actividadescentro\application\CentroEncargadoReordenar;
use src\actividadescentro\application\CentroEncargadoMutationCtx;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/**
 * Actividad y centro salen de la cápsula `ctx` (la firman los datos de la
 * pantalla de centros encargados). Responde JSON `{success, mensaje, data}`.
 */
$input = CentroEncargadoMutationCtx::abrir($_POST, CentroEncargadoMutationCtx::REORDENAR);
if ($input === null) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
$input['num_orden'] = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'num_orden');
/** @var CentroEncargadoReordenar $useCase */
$useCase = DependencyResolver::get(CentroEncargadoReordenar::class);
$error_txt = $useCase->execute($input);
ContestarJson::enviar($error_txt, 'ok');
