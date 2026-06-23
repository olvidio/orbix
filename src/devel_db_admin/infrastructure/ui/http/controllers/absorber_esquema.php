<?php

declare(strict_types=1);

/**
 * JSON `{ "lines": string[] }` para la absorción de esquema (POST `esquema_matriz`, `esquema_del`).
 */

use src\shared\web\ContestarJson;
use src\devel_db_admin\application\AbsorberEsquema;
use src\shared\infrastructure\DependencyResolver;


/** @var AbsorberEsquema $useCase */
$useCase = DependencyResolver::get(AbsorberEsquema::class);

$esquemaMatriz = (string) filter_post('esquema_matriz');
$esquemaDel = (string) filter_post('esquema_del');

$result = $useCase->execute($esquemaMatriz, $esquemaDel);

ContestarJson::enviar('', ['lines' => $result->lines, 'errores' => $result->errores]);
