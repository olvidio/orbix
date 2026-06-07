<?php

use src\notas\application\ActaPdfSubir;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\ui\http\MultipartUploadGuard;
use src\shared\web\ContestarJson;

MultipartUploadGuard::exitIfPostTooLargeJson();

/** @var array<string, array<string, mixed>> $files */
$files = [];
foreach ($_FILES as $key => $file) {
    if (is_string($key) && is_array($file)) {
        $files[$key] = $file;
    }
}

$r = (DependencyResolver::get(ActaPdfSubir::class))->execute($_POST, $files);
ContestarJson::enviar($r['error'], 'ok', $r['http_status']);
