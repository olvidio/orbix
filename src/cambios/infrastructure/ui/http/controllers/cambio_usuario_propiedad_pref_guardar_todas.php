<?php
/**
 * Endpoint JSON: sincroniza las `CambioUsuarioPropiedadPref` para un
 * `CambioUsuarioObjetoPref`. Crea, actualiza o elimina en funcion del POST.
 *
 * Sucesor de la rama `guardar_propiedades` de `apps/cambios/controller/usuario_avisos_pref_ajax.php`.
 */

use src\cambios\application\CambioUsuarioPropiedadPrefGuardarTodas;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

// Esta mutacion necesita leer claves dinamicas (nombradas por `$objeto[]` y
// `${objeto}_${nom_prop}_cond`) por lo que pasamos todo el POST.
$input = $_POST ?? [];

$result = CambioUsuarioPropiedadPrefGuardarTodas::execute($input);
$error = (string)$result['error'];

ContestarJson::enviar($error, []);
