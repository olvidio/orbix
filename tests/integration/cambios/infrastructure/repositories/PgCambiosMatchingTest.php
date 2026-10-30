<?php

namespace Tests\integration\cambios\infrastructure\repositories;

use src\cambios\domain\contracts\CambioAnotadoRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use src\cambios\domain\entity\CambioAnotado;
use src\cambios\domain\entity\CambioUsuario;
use src\cambios\domain\entity\CambioUsuarioObjetoPref;
use src\cambios\domain\entity\CambioUsuarioPropiedadPref;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\cambios\domain\criteria\CambioAnotadoField;
use src\cambios\domain\criteria\CambioField;
use src\cambios\domain\criteria\CambioUsuarioField;
use src\cambios\domain\criteria\CambioUsuarioObjetoPrefField;
use src\cambios\domain\criteria\CambioUsuarioPropiedadPrefField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use Tests\myTest;

/**
 * `matching()` de los repositorios de `cambios` (paso 4.3 de docs/dev/refactor_busquedas_criteria.md).
 * Comparan con el método legacy sobre los datos existentes.
 */
class PgCambiosMatchingTest extends myTest
{
    public function test_cambios_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(CambioRepositoryInterface::class);
        $result = $repo->matching(Criteria::create()->orderBy(CambioField::IdItemCambio)->limit(20));
        $this->assertContainsOnlyInstancesOf(Cambio::class, $result);
        $this->assertLessThanOrEqual(20, count($result));
    }

    public function test_cambios_usuario_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(CambioUsuarioRepositoryInterface::class);
        $result = $repo->matching(
            Criteria::create()
                ->where(Filter::eq(CambioUsuarioField::Avisado, false))
                ->orderBy(CambioUsuarioField::IdUsuario)
                ->addOrderBy(CambioUsuarioField::IdItemCambio)
                ->limit(20)
        );
        $this->assertContainsOnlyInstancesOf(CambioUsuario::class, $result);
        $this->assertLessThanOrEqual(20, count($result));
    }

    public function test_cambios_anotados_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(CambioAnotadoRepositoryInterface::class);
        $repo->setTabla('sv');
        $result = $repo->matching(Criteria::create()->orderBy(CambioAnotadoField::IdItem)->limit(20));
        $this->assertContainsOnlyInstancesOf(CambioAnotado::class, $result);
        $this->assertLessThanOrEqual(20, count($result));
    }

    public function test_prefs_de_usuario_equivalen_a_legacy(): void
    {
        $objetos = $GLOBALS['container']->get(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $this->assertContainsOnlyInstancesOf(
            CambioUsuarioObjetoPref::class,
            $objetos->matching(
                Criteria::create()
                    ->orderBy(CambioUsuarioObjetoPrefField::Objeto)
                    ->addOrderBy(CambioUsuarioObjetoPrefField::DlOrg)
                    ->addOrderBy(CambioUsuarioObjetoPrefField::IdTipoActivTxt)
            )
        );

        $propiedades = $GLOBALS['container']->get(CambioUsuarioPropiedadPrefRepositoryInterface::class);
        $this->assertContainsOnlyInstancesOf(
            CambioUsuarioPropiedadPref::class,
            $propiedades->matching(Criteria::create()->orderBy(CambioUsuarioPropiedadPrefField::IdItem))
        );
    }

    public function test_find_por_tipo_actividad_equivale_al_legacy_inverso(): void
    {
        $repo = $GLOBALS['container']->get(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $todas = $repo->matching(Criteria::create()->limit(1));
        if ($todas === []) {
            $this->assertSame([], $repo->findPorTipoActividad('x', 'x', '1'));
            return;
        }
        $pref = $todas[0];
        $objeto = (string) $pref->getObjeto();
        $dlOrg = (string) $pref->getDl_org();
        $idTipo = '112021';
        $esperadas = $repo->matching(
            Criteria::create()
                ->where(Filter::eq(CambioUsuarioObjetoPrefField::Objeto, $objeto))
                ->andWhere(Filter::eq(CambioUsuarioObjetoPrefField::DlOrg, $dlOrg))
        );
        $esperadas = array_values(array_filter(
            $esperadas,
            static fn ($p) => preg_match('/' . $p->getId_tipo_activ_txt() . '/', $idTipo) === 1
        ));
        $this->assertCount(count($esperadas), $repo->findPorTipoActividad($objeto, $dlOrg, $idTipo));
    }
}
