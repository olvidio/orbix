<?php

declare(strict_types=1);

namespace Tests\integration\ubis\application;

use src\ubis\application\HomeUbisData;
use src\ubis\application\UbiFactory;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\criteria\CentroDlField;
use src\shared\domain\criteria\Criteria;
use Tests\myTest;

/**
 * Creación de la ubi correcta por `id_ubi` y ficha de inicio, sobre datos reales (solo lectura).
 * También cubre `UbiContactsTrait` (direcciones, teléfonos, correo) a través de las entidades reales.
 */
final class UbiFactoryHomeIntegracionTest extends myTest
{
    private UbiFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->factory = $GLOBALS['container']->get(UbiFactory::class);
    }

    private function idCentroDl(): ?int
    {
        $repo = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        foreach ($repo->matching(Criteria::create()->orderBy(CentroDlField::IdUbi)->limit(50)) as $c) {
            return $c->getId_ubi();
        }

        return null;
    }

    public function test_un_id_inexistente_no_da_ubi_ni_ficha(): void
    {
        $this->assertNull($this->factory->newUbi(999999999));
        $this->assertSame([], $GLOBALS['container']->get(HomeUbisData::class)->execute(999999999));
    }

    public function test_un_centro_de_mi_dl_se_resuelve_a_su_entidad_con_los_datos_de_la_ficha(): void
    {
        $id = $this->idCentroDl();
        if ($id === null) {
            $this->markTestSkipped('No hay centros de la dl en la BD.');
        }

        $ubi = $this->factory->newUbi($id);
        $ficha = $GLOBALS['container']->get(HomeUbisData::class)->execute($id);

        $this->assertNotNull($ubi);
        $this->assertSame($id, $ubi->getId_ubi());
        $this->assertSame($id, $ficha['id_ubi']);
        $this->assertSame($ubi->getNombre_ubi(), $ficha['nombre_ubi']);
        $this->assertSame('u', $ficha['pau']);
        $this->assertContains($ficha['obj_pau'], ['Centro', 'CentroDl', 'CentroEx', '']);
        $this->assertSame($ficha['obj_pau'] === '' ? '' : 'centro', $ficha['ubi']);
        foreach (['direccion', 'poblacion', 'c_p', 'telfs', 'fax', 'mails'] as $k) {
            $this->assertIsString($ficha[$k], $k);
        }
    }

    public function test_el_trait_de_contactos_es_coherente_con_las_direcciones_de_la_ficha(): void
    {
        $id = $this->idCentroDl();
        if ($id === null) {
            $this->markTestSkipped('No hay centros de la dl en la BD.');
        }
        $ubi = $this->factory->newUbi($id);

        $dirs = array_filter($ubi->getDireccionesGral());
        $tel = $ubi->getTeleco('telf', '*', ' / ');
        $mail = $ubi->emailPrincipalOPrimero();

        $this->assertContainsOnlyInstancesOf(\src\ubis\domain\entity\Direccion::class, $dirs);
        $this->assertIsString($tel);
        $this->assertStringNotContainsString('.-<br>', $ubi->getTeleco('telf', '*', ' / '), 'con separador explícito no se usa el de por defecto');
        // `emailPrincipalOPrimero` devuelve el primer correo: debe estar entre los del teléfono-tipo 3.
        if ($mail !== '') {
            $this->assertStringContainsString($mail, $ubi->getTeleco('e-mail', '', ' | '));
        }
    }

    public function test_un_id_con_prefijo_de_sf_inexistente_no_da_ubi(): void
    {
        // Los ids que empiezan por 2 son de centros de sf: se buscan por la rama de sf y, si no existe, como casa.
        $ubi = $this->factory->newUbi(2999999);

        $this->assertNull($ubi);
    }
}
