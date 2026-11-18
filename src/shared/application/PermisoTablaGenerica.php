<?php

declare(strict_types=1);

namespace src\shared\application;

use src\dossiers\application\PermDossier;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\menus\domain\contracts\MenuDbRepositoryInterface;
use src\menus\domain\criteria\MenuDbField;
use src\menus\domain\PermisoMenuBits;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Quién puede dar de alta, modificar o borrar en una tabla de la pantalla genérica (`tablaDB_*`): la misma
 * regla con la que se le muestra la tabla.
 *
 * - Tabla de un dossier (la clase `Info*` declara `getId_dossier()`): el permiso del dossier sobre esa
 *   persona, actividad o ubi ({@see PermDossier::permiso()} = 3, lectura y escritura).
 * - Tabla de catálogo: el permiso de menú de alguna entrada de la dl que la abre (`parametros` con
 *   `clase_info=<clase>`), igual que se decide si la entrada sale en el menú.
 * - Una tabla que no abre ningún menú ni es de un dossier: solo el permiso de menú `admin_sv`.
 *
 * Los permisos de menú del usuario son los de la sesión (`iPermMenus`). Revisión de seguridad del 2026-11-18.
 */
final class PermisoTablaGenerica
{
    public function __construct(
        private readonly MenuDbRepositoryInterface $menuDbRepository,
        private readonly TipoDossierRepositoryInterface $tipoDossierRepository,
    ) {
    }

    /**
     * @param string $claseInfo clase `Info*` (puede llegar codificada para URL y con «\» inicial)
     * @param int|null $idDossier tipo de dossier si la clase es la tabla de un dossier
     * @param int $idPau persona, actividad o ubi del dossier (0 en las tablas de catálogo)
     */
    public function puedeModificar(string $claseInfo, ?int $idDossier, int $idPau): bool
    {
        if ($idDossier !== null) {
            return $this->puedeModificarDossier($idDossier, $idPau);
        }

        $clase = self::normalizar($claseInfo);
        $permisoUsuario = self::permisoMenuUsuario();
        $hayMenu = false;
        $menus = $this->menuDbRepository->matching(
            Criteria::create()->where(Filter::matches(MenuDbField::Parametros, 'clase_info='))
        );
        foreach ($menus as $menu) {
            parse_str((string) $menu->getParametros(), $parametros);
            $claseMenu = $parametros['clase_info'] ?? '';
            if (!is_string($claseMenu) || self::normalizar($claseMenu) !== $clase) {
                continue;
            }
            $hayMenu = true;
            if (($permisoUsuario & (int) $menu->getMenu_perm()) !== 0) {
                return true;
            }
        }
        if ($hayMenu) {
            return false;
        }

        return ($permisoUsuario & PermisoMenuBits::map()['admin_sv']) !== 0;
    }

    private function puedeModificarDossier(int $idDossier, int $idPau): bool
    {
        $tipo = $this->tipoDossierRepository->findById($idDossier);
        if ($tipo === null) {
            return false;
        }

        return PermDossier::permiso(
            $tipo->getPermiso_lectura(),
            (int) ($tipo->getPermiso_escritura() ?? 0),
            $tipo->isDepende_modificar(),
            $tipo->getTabla_from(),
            $idPau
        ) === 3;
    }

    private static function normalizar(string $clase): string
    {
        return ltrim(urldecode($clase), '\\');
    }

    private static function permisoMenuUsuario(): int
    {
        $perm = $_SESSION['iPermMenus'] ?? 0;

        return is_numeric($perm) ? (int) $perm : 0;
    }
}
