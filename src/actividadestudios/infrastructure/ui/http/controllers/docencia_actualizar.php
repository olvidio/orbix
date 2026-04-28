<?php
/**
 * Ejecuta {@see DocenciaActualizar} (actualizar dossier docencia STGR).
 * Consumo desde `frontend/actividadestudios/controller/actualizar_docencia.php` via PostRequest.
 */

use frontend\shared\web\ContestarJson;
use src\actividadestudios\application\DocenciaActualizar;

$txt_rta = DocenciaActualizar::execute($_POST);

ContestarJson::enviar('', ['txt_rta' => $txt_rta]);
