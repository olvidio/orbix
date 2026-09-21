<?php

/**
 * Validación del parámetro `h` (integridad URL/form) tras el bootstrap en
 * {@see global_object.inc}. Vive en `frontend/` para que `src/shared` no
 * dependa de `frontend\shared\security\HashF`.
 */

namespace core;

use frontend\shared\security\HashF;

session_write_close();
$oValidator = new HashF();
$a_data = (!empty($_POST)) ? $_POST : ((isset($_GET['h'])) ? $_GET : []);
echo $oValidator->validatePost($a_data);
