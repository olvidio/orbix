<?php

namespace frontend\shared\security;

/**
 * HashF — Hash de la capa de presentación (frontend).
 *
 * Rol: anti-CSRF de endpoints `frontend/` e integridad de URLs en
 * navegaciones frontend ↔ frontend (listas, filtros, paginación,
 * scroll memory, forms internos).
 *
 * **Simétrica**: cualquier código de `frontend/` (y de `apps/` durante
 * la transición) puede firmar y cualquier código de `frontend/` puede
 * validar. Secreto derivado de la sesión PHP, igual que hoy.
 *
 * **NO usar** para firmar peticiones destinadas a endpoints de `src/`.
 * Esas peticiones se protegen con `HashB` (asimétrico, backend-only)
 * en forma de cápsula opaca transportada por el navegador. Ver
 * `documentacion/hash_arquitectura.md`.
 *
 * Fase 1 (actual): `HashF` es una subclase transparente de `\web\Hash`
 * sin cambios de comportamiento. Su valor en esta fase es **declarar el
 * rol**: allí donde el código nuevo o migrado diga `use
 * frontend\shared\security\HashF`, quien lea el código sabe que está
 * firmando para la capa UI y no para el backend.
 *
 * Fase posterior: `HashF` conservará el protocolo actual
 * (`h`, `hh`, `hhc`, `hno`, `hchk`, `hnov`, `horig`, `hhorig`, `hpos`),
 * mientras que `HashB` no expondrá ese protocolo a la UI (solo el token
 * opaco `ctx`).
 *
 * @see \web\Hash
 * @see documentacion/hash_arquitectura.md
 */
class HashF extends \web\Hash
{
}
