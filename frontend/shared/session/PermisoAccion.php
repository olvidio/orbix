<?php

declare(strict_types=1);

namespace frontend\shared\session;

use src\procesos\domain\PermAccionBits;

/**
 * Nombres de acción para `havePermActiv()`, `onlyPerm()` y `have_perm_action()` (plantillas Twig).
 *
 * Cada constante apunta a la de {@see PermAccionBits}: no se pueden desincronizar y un nombre
 * mal escrito falla al ejecutar en lugar de denegar en silencio.
 */
final class PermisoAccion
{
    public const NADA = PermAccionBits::NADA;
    public const OCUPADO = PermAccionBits::OCUPADO;
    public const VER = PermAccionBits::VER;
    public const MODIFICAR = PermAccionBits::MODIFICAR;
    public const CREAR = PermAccionBits::CREAR;
    public const BORRAR = PermAccionBits::BORRAR;
}
