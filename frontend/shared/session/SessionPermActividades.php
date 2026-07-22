<?php

declare(strict_types=1);

namespace frontend\shared\session;

/**
 * Wrapper de permisos de actividad en sesión (`$_SESSION['oPermActividades']`)
 * sin `use src\...` en callers.
 */
final class SessionPermActividades
{
    public static function isPresent(): bool
    {
        $o = $_SESSION['oPermActividades'] ?? null;

        return $o instanceof \src\permisos\domain\PermisosActividades;
    }

    private static function engine(): ?\src\permisos\domain\PermisosActividades
    {
        $o = $_SESSION['oPermActividades'] ?? null;

        return $o instanceof \src\permisos\domain\PermisosActividades ? $o : null;
    }

    public static function isTrueEngine(): bool
    {
        $o = self::engine();

        return $o instanceof \src\permisos\domain\PermisosActividadesTrue;
    }

    public static function setActividad(int $idActiv, string $idTipoActiv, string $dlOrg): void
    {
        self::engine()?->setActividad($idActiv, $idTipoActiv, $dlOrg);
    }

    /**
     * @param list<int> $fases
     */
    public static function setFasesCompletadas(array $fases): void
    {
        self::engine()?->setFasesCompletadas($fases);
    }

    /**
     * @return SessionPermActivResult|null
     */
    public static function getPermisoActual(string $afecta): ?SessionPermActivResult
    {
        $engine = self::engine();
        if ($engine === null) {
            return null;
        }
        $raw = $engine->getPermisoActual($afecta);

        return new SessionPermActivResult($raw);
    }
}
