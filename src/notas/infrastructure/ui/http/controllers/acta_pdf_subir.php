<?php

use src\notas\application\ActaPdfSubir;
use src\shared\infrastructure\ui\http\MultipartUploadGuard;
use frontend\shared\web\ContestarJson;

MultipartUploadGuard::exitIfPostTooLargeJson();

$r = ActaPdfSubir::execute($_POST, $_FILES);
ContestarJson::enviar($r['error'], 'ok', $r['http_status']);
