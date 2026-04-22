<?php

// Rutas del modulo `planning`. Cada endpoint vive en
// `src/planning/infrastructure/ui/http/controllers/` y responde JSON
// mediante `web\ContestarJson::enviar(...)`.
//
// Registro inicial (slice 1 de la migracion): por ahora solo existe el
// flujo de `leyenda`, que es puramente presentacion y no necesita
// endpoint en `src/`. Los siguientes slices iran anadiendo rutas.
return static function ($r) {
};
