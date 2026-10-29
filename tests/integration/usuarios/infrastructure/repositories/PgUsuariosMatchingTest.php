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
    public function test_usuarios_con_operador_y_orden_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $this->assertEquals(
            $repo->getUsuarios(['id_role' => 1, '_ordre' => 'usuario', '_limit' => 20], ['id_role' => '>']),
            $repo->matching(
                Criteria::create()
                    ->where(Filter::gt(UsuarioField::IdRole, 1))
                    ->orderBy(UsuarioField::Usuario)
                    ->limit(20)
            )
        );
    }

    public function test_usuarios_sin_acentos_y_csv_id_pau_no_nulo(): void
    {
        $repo = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $this->assertEquals(
            $repo->getUsuarios(['csv_id_pau' => 'x'], ['csv_id_pau' => 'IS NOT NULL']),
            $repo->matching(Criteria::create()->where(Filter::isNotNull(UsuarioField::CsvIdPau)))
        );
        $this->assertEquals(
            $repo->getUsuarios(['usuario' => 'dani'], ['usuario' => 'sin_acentos']),
            $repo->matching(Criteria::create()->where(Filter::matchesUnaccented(UsuarioField::Usuario, 'dani')))
        );
    }

    public function test_grupos_y_usuarios_grupos_equivalen_a_legacy(): void
    {
        $grupos = $GLOBALS['container']->get(GrupoRepositoryInterface::class);
        $this->assertEquals(
            $grupos->getGrupos(['id_usuario' => '^5', '_ordre' => 'usuario'], ['id_usuario' => '~']),
            $grupos->matching(
                Criteria::create()->where(Filter::matches(GrupoField::IdUsuario, '^5'))->orderBy(GrupoField::Usuario)
            )
        );

        $usuariosGrupos = $GLOBALS['container']->get(UsuarioGrupoRepositoryInterface::class);
        $this->assertEquals(
            $usuariosGrupos->getUsuariosGrupos(['id_usuario' => 1]),
            $usuariosGrupos->matching(Criteria::create()->where(Filter::eq(UsuarioGrupoField::IdUsuario, 1)))
        );
    }

    public function test_roles_locales_permmenus_y_preferencias_equivalen_a_legacy(): void
    {
        $roles = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $this->assertEquals(
            $roles->getRoles(['_ordre' => 'role']),
            $roles->matching(Criteria::create()->orderBy(RoleField::Role))
        );

        $locales = $GLOBALS['container']->get(LocalRepositoryInterface::class);
        $this->assertEquals(
            $locales->getLocales(['_ordre' => 'active DESC,nom_idioma ASC']),
            $locales->matching(
                Criteria::create()->orderBy(LocalField::Active, OrderDirection::Desc)->addOrderBy(LocalField::NomIdioma)
            )
        );

        $permMenus = $GLOBALS['container']->get(PermMenuRepositoryInterface::class);
        $this->assertEquals(
            $permMenus->getPermMenus(['id_usuario' => 1]),
            $permMenus->matching(Criteria::create()->where(Filter::eq(PermMenuField::IdUsuario, 1)))
        );

        $preferencias = $GLOBALS['container']->get(PreferenciaRepositoryInterface::class);
        $this->assertEquals(
            $preferencias->getPreferencias(['id_usuario' => 1, 'tipo' => 'ultima_plantilla']),
            $preferencias->matching(
                Criteria::create()
                    ->where(Filter::eq(PreferenciaField::IdUsuario, 1))
                    ->andWhere(Filter::eq(PreferenciaField::Tipo, 'ultima_plantilla'))
            )
        );
    }
}
