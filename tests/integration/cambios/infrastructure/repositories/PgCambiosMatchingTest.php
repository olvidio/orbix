<?php

namespace Tests\integration\cambios\infrastructure\repositories;

use src\cambios\domain\contracts\CambioAnotadoRepositoryInterface;
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
        $this->assertEquals(
            $repo->getCambios(['_ordre' => 'id_item_cambio', '_limit' => 20]),
            $repo->matching(Criteria::create()->orderBy(CambioField::IdItemCambio)->limit(20))
        );
    }

    public function test_cambios_usuario_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(CambioUsuarioRepositoryInterface::class);
        $this->assertEquals(
            $repo->getCambiosUsuario(['avisado' => 'false', '_ordre' => 'id_usuario,id_item_cambio', '_limit' => 20]),
            $repo->matching(
                Criteria::create()
                    ->where(Filter::eq(CambioUsuarioField::Avisado, false))
                    ->orderBy(CambioUsuarioField::IdUsuario)
                    ->addOrderBy(CambioUsuarioField::IdItemCambio)
                    ->limit(20)
            )
        );
    }

    public function test_cambios_anotados_equivalen_a_legacy(): void
    {
        $repo = $GLOBALS['container']->get(CambioAnotadoRepositoryInterface::class);
        $repo->setTabla('sv');
        $this->assertEquals(
            $repo->getCambiosAnotados(['_ordre' => 'id_item', '_limit' => 20]),
            $repo->matching(Criteria::create()->orderBy(CambioAnotadoField::IdItem)->limit(20))
        );
    }

    public function test_prefs_de_usuario_equivalen_a_legacy(): void
    {
        $objetos = $GLOBALS['container']->get(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $this->assertEquals(
            $objetos->getCambioUsuarioObjetoPrefs(['_ordre' => 'objeto, dl_org, id_tipo_activ_txt']),
            $objetos->matching(
                Criteria::create()
                    ->orderBy(CambioUsuarioObjetoPrefField::Objeto)
                    ->addOrderBy(CambioUsuarioObjetoPrefField::DlOrg)
                    ->addOrderBy(CambioUsuarioObjetoPrefField::IdTipoActivTxt)
            )
        );

        $propiedades = $GLOBALS['container']->get(CambioUsuarioPropiedadPrefRepositoryInterface::class);
        $this->assertEquals(
            $propiedades->getCambioUsuarioPropiedadPrefs(['_ordre' => 'id_item']),
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
        $this->assertEquals(
            $repo->getCambioUsuarioObjetoPrefs(
                [
                    'objeto' => $objeto,
                    'dl_org' => $dlOrg,
                    'id_tipo_activ_txt' => $idTipo,
                    '_ordre' => 'aviso_tipo,id_usuario,id_tipo_activ_txt DESC',
                ],
                ['id_tipo_activ_txt' => '~INV']
            ),
            $repo->findPorTipoActividad($objeto, $dlOrg, $idTipo)
        );
    }
}
