<?php

use src\menus\infrastructure\persistence\postgresql\PgMenusPlantillaImportador;
use src\shared\infrastructure\GlobalPdo;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

/**
 * Sustituye los menús de la aplicación por los de una plantilla. La lógica está en {@see PgMenusPlantillaImportador}:
 * con una plantilla sin menús no cambia nada, y si algo falla quedan los menús de antes.
 */
try {
    HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_importar'),
        'menus_importar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$importador = new PgMenusPlantillaImportador(GlobalPdo::get('oDBE'), GlobalPdo::get('oDBPC'));
$error_txt = $importador->importar(\src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_template_menu'));

ContestarJson::enviar($error_txt, 'ok');
