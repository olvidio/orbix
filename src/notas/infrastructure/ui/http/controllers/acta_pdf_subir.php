<?php

use src\notas\application\ActaPdfSubir;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\ui\http\MultipartUploadGuard;
use src\shared\web\ContestarJson;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;

MultipartUploadGuard::exitIfPostTooLargeJson();
try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_pdf_subir'),
        'acta_pdf_subir'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
$input = $_POST;
$input['acta_num'] = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'acta');

/** @var array<string, array<string, mixed>> $files */
$files = [];
foreach ($_FILES as $key => $file) {
    if (is_string($key) && is_array($file)) {
        $files[$key] = $file;
    }
}

$r = (DependencyResolver::get(ActaPdfSubir::class))->execute($input, $files);
ContestarJson::enviar($r['error'], 'ok', $r['http_status']);
