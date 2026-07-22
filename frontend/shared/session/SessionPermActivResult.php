<?php

declare(strict_types=1);

namespace frontend\shared\session;

/**
 * Resultado opaco de {@see SessionPermActividades::getPermisoActual}.
 */
final class SessionPermActivResult
{
    public function __construct(private readonly object $inner)
    {
    }

    public function onlyPerm(string $permiso): bool
    {
        if (!method_exists($this->inner, 'only_perm')) {
            return false;
        }

        return (bool) $this->inner->only_perm($permiso);
    }

    public function havePermActiv(string $permiso): bool
    {
        if (!method_exists($this->inner, 'have_perm_activ')) {
            return false;
        }

        return (bool) $this->inner->have_perm_activ($permiso);
    }

    /**
     * Reenvía métodos legacy del objeto interno (p. ej. `have_perm_action` en Twig).
     *
     * @param list<mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (!method_exists($this->inner, $name)) {
            return false;
        }

        return $this->inner->{$name}(...$arguments);
    }
}
