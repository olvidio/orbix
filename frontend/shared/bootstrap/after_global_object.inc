<?php

/**
 * Validación del parámetro `h` (integridad URL/form) tras el bootstrap en
 * {@see global_object.inc}. Vive en `frontend/` para que `src/shared` no
 * dependa de `frontend\shared\security\HashF`.
 */

namespace core;

use frontend\shared\security\HashF;

session_write_close();
\src\shared\infrastructure\ui\http\AccesoRolPau::comprobarPeticionActual();
(new HashF())->validatePost(HashF::datosDeLaPeticion());
