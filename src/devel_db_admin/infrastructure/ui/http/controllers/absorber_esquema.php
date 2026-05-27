<?php

declare(strict_types=1);

/**
 * JSON `{ "lines": string[] }` para la absorción de esquema (POST `esquema_matriz`, `esquema_del`).
 */

use src\shared\web\ContestarJson;
use src\devel_db_admin\application\AbsorberEsquema;

require_once 'frontend/shared/global_header_front.inc';

$esquemaMatriz = (string) filter_input(INPUT_POST, 'esquema_matriz');
$esquemaDel = (string) filter_input(INPUT_POST, 'esquema_del');

$result = (new AbsorberEsquema($GLOBALS['container']))->execute($esquemaMatriz, $esquemaDel);

ContestarJson::enviar('', ['lines' => $result->lines, 'errores' => $result->errores]);
