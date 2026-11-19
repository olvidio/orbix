<?php

/**
 * Estructura de una tabla para el generador de código (`frontend/devel_codegen/controller/factory.php`).
 * POST `db` (tramity, davical, comun, sv, sf, sv-e) y `tabla` (`tabla` o `esquema.tabla`)
 * → `{primary_key, columnas, hoy}`. Solo lectura.
 */

use src\devel_codegen\infrastructure\persistence\TablaEstructuraPg;
use src\shared\domain\helpers\FilterPostGet;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\web\ContestarJson;

// Herramienta sobre todos los esquemas: solo quien ve las herramientas de base de datos en el menú.
if (!\src\devel_db_admin\infrastructure\ui\http\PermisoHerramientasBdGuard::permiteEsquemas()) {
    \src\shared\web\ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qdb = (string)FilterPostGet::post('db');
$Qtabla = (string)FilterPostGet::post('tabla');
if ($Qdb === '' || $Qtabla === '') {
    ContestarJson::enviar(sprintf(_("Faltan parámetros: %s"), 'db, tabla'), 'none');
    return;
}

try {
    $estructura = (new TablaEstructuraPg())->leer($Qdb, $Qtabla);
} catch (\Throwable $e) {
    ContestarJson::enviar($e->getMessage(), 'none');
    return;
}
if ($estructura['primary_key'] === []) {
    ContestarJson::enviar('Quizà falta definir la clave primaria', 'none');
    return;
}

ContestarJson::enviar('', $estructura + ['hoy' => (new DateTimeLocal())->getFromLocal()]);
