<?php

declare(strict_types=1);

use frontend\devel_db_admin\helpers\DevelDbAdminPayload;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
// copiar definición y datos de sv
// las definiciones de tablas padre ya las tengo: todas las global y public de sv las pongo en sv-e.
// Para todos los esquemas

$Qtabla = (string) filter_input(INPUT_POST, 'tabla');

$data = PostRequest::getDataFromUrl('/src/devel_db_admin/mover_tabla', [
    'tabla' => $Qtabla,
]);

print_r($data['a_esquemas'] ?? []);

foreach (DevelDbAdminPayload::avisosList($data['lines'] ?? []) as $line) {
    echo DevelDbAdminPayload::lineString($line);
}
