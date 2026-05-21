<?php

/** Acceso legacy desde public/ayuda: delega en el controlador del frontend. */
$GLOBALS['ayuda_standalone_shell'] = true;
require_once __DIR__ . '/../../frontend/shared/controller/ayuda_index.php';
