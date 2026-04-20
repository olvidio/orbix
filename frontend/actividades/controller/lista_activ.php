<?php
/*
 * Wrapper DEPRECADO. La ruta canonica nueva es
 * /src/actividades/lista_activ (src/actividades/infrastructure/ui/http/controllers/lista_activ.php).
 *
 * Se conserva solo por compatibilidad con menus/enlaces legados que apunten
 * al path antiguo. Se requiere global_header_front.inc para asegurar sesion
 * y validacion de hash cuando no se accede por el front-controller.
 */
require_once __DIR__ . '/../../shared/global_header_front.inc';
require __DIR__ . '/../../../src/actividades/infrastructure/ui/http/controllers/lista_activ.php';
