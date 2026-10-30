<?php

namespace Tests\integration\usuarios\infrastructure\repositories;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\OrderDirection;
use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\criteria\GrupoField;
use src\usuarios\domain\criteria\LocalField;
use src\usuarios\domain\criteria\PermMenuField;
use src\usuarios\domain\criteria\PreferenciaField;
use src\usuarios\domain\criteria\RoleField;
use src\usuarios\domain\criteria\UsuarioField;
use src\usuarios\domain\criteria\UsuarioGrupoField;
use Tests\myTest;

/**
 * `matching()` de los repositorios de `usuarios` (paso 4.3 de docs/dev/refactor_busquedas_criteria.md).
 * Comparan con el método legacy sobre los datos existentes.
 */
class PgUsuariosMatchingTest extends myTest
{
    public function test_usuarios_con_operador_y_orden(): void
    {
        $repo = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $res = $repo->matching(
            Criteria::create()
                ->where(Filter::gt(UsuarioField::IdRole, 1))
                ->orderBy(UsuarioField::Usuario)
                ->limit(20)
        );
        $this->assertLessThanOrEqual(20, count($res));
        foreach ($res as $u) {
            $this->assertGreaterThan(1, $u->getId_role());
        }
    }

    public function test_usuarios_sin_acentos_y_csv_id_pau_no_nulo(): void
    {
        $repo = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $this->assertIsArray($repo->matching(Criteria::create()->where(Filter::isNotNull(UsuarioField::CsvIdPau))));
        $this->assertIsArray($repo->matching(Criteria::create()->where(Filter::matchesUnaccented(UsuarioField::Usuario, 'dani'))));
    }

    public function test_grupos_y_usuarios_grupos(): void
    {
        $grupos = $GLOBALS['container']->get(GrupoRepositoryInterface::class);
        $this->assertIsArray(
            $grupos->matching(
                Criteria::create()->where(Filter::matches(GrupoField::IdUsuario, '^5'))->orderBy(GrupoField::Usuario)
            )
        );

        $usuariosGrupos = $GLOBALS['container']->get(UsuarioGrupoRepositoryInterface::class);
        foreach ($usuariosGrupos->matching(Criteria::create()->where(Filter::eq(UsuarioGrupoField::IdUsuario, 1))) as $ug) {
            $this->assertSame(1, $ug->getId_usuario());
        }
    }

    public function test_roles_locales_permmenus_y_preferencias(): void
    {
        $roles = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $this->assertIsArray($roles->matching(Criteria::create()->orderBy(RoleField::Role)));

        $locales = $GLOBALS['container']->get(LocalRepositoryInterface::class);
        $this->assertEquals(
            $locales->getLocales(['_ordre' => 'active DESC,nom_idioma ASC']),
            $locales->matching(
                Criteria::create()->orderBy(LocalField::Active, OrderDirection::Desc)->addOrderBy(LocalField::NomIdioma)
            )
        );

        $permMenus = $GLOBALS['container']->get(PermMenuRepositoryInterface::class);
        $this->assertIsArray($permMenus->matching(Criteria::create()->where(Filter::eq(PermMenuField::IdUsuario, 1))));

        $preferencias = $GLOBALS['container']->get(PreferenciaRepositoryInterface::class);
        $this->assertIsArray(
            $preferencias->matching(
                Criteria::create()
                    ->where(Filter::eq(PreferenciaField::IdUsuario, 1))
                    ->andWhere(Filter::eq(PreferenciaField::Tipo, 'ultima_plantilla'))
            )
        );
    }
}
