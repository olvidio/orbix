<?php

/** Acceso legacy desde public/ayuda: delega en el controlador del frontend. */
$GLOBALS['manual_standalone_shell'] = true;
require_once __DIR__ . '/../../frontend/shared/controller/manual.php';
