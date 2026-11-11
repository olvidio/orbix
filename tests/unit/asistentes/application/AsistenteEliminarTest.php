<?php

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\criteria\MatriculaField;
use src\actividadestudios\domain\entity\Matricula;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\asistentes\application\AsistenteEliminar;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\entity\Asistente;
use src\dossiers\domain\contracts\DossierRepositoryInterface;

final class AsistenteEliminarTest extends TestCase
{
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    private function createSut(
        ?AsistenteApplicationService $app = null,
        ?MatriculaRepositoryInterface $matRepo = null,
        ?DossierRepositoryInterface $dosRepo = null,
    ): AsistenteEliminar {
        $app ??= $this->createMock(AsistenteApplicationService::class);
        $matRepo ??= $this->createMock(MatriculaRepositoryInterface::class);
        $dosRepo ??= $this->createMock(DossierRepositoryInterface::class);
        return new AsistenteEliminar($app, $matRepo, $dosRepo);
    }

    public function test_faltan_ids(): void
    {
        $sut = $this->createSut();

        $this->assertNotSame('', $sut->execute(['id_activ' => 0, 'id_nom' => 1]));
    }

    public function test_no_encontrado(): void
    {
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(10, 20)->willReturn(null);

        $sut = $this->createSut($app);

        $this->assertNotSame('', $sut->execute(['id_activ' => 10, 'id_nom' => 20]));
    }

    public function test_sin_permiso_modificar(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(false);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($o);
        $app->expects($this->never())->method('eliminar');

        $sut = $this->createSut($app);

        $this->assertNotSame('', $sut->execute(['id_activ' => 1, 'id_nom' => 2]));
    }

    public function test_falla_eliminar(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($o);
        $app->method('eliminar')->willReturn(false);

        $sut = $this->createSut($app);

        $this->assertNotSame('', $sut->execute(['id_activ' => 1, 'id_nom' => 2]));
    }

    public function test_exito_sin_matriculas_y_parse_sel_p(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(7, 3)->willReturn($o);
        $app->method('eliminar')->with($o)->willReturn(true);

        $matRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $matRepo->method('matching')
            ->with(Criteria::create()
                ->where(Filter::eq(MatriculaField::IdActiv, 7))
                ->andWhere(Filter::eq(MatriculaField::IdNom, 3)))
            ->willReturn([]);

        $dosRepo = $this->createMock(DossierRepositoryInterface::class);
        $dosRepo->method('findByPk')->willReturn(null);

        $sut = $this->createSut($app, $matRepo, $dosRepo);

        $msg = $sut->execute([
            'pau' => 'p',
            'sel' => ['7#'],
            'id_pau' => 3,
        ]);
        $this->assertSame('', $msg);
    }

    private function asistenteDeMiDl(int $idActiv, int $idNom): Asistente
    {
        $a = new Asistente();
        $a->setId_activ($idActiv);
        $a->setId_nom($idNom);
        $a->setDl_responsable('dl');

        return $a;
    }

    /**
     * Antes este test simulaba `Matricula::DBEliminar()`, que el caso de uso no llama, y pasaba solo porque el
     * `Eliminar()` del repositorio simulado devuelve `false` por defecto.
     */
    public function test_si_falla_el_borrado_de_una_matricula_se_avisa_por_cada_una_y_se_siguen_borrando(): void
    {
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($this->asistenteDeMiDl(1, 2));
        $app->method('eliminar')->willReturn(true);
        $m1 = new Matricula();
        $m2 = new Matricula();
        $matRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $matRepo->method('matching')->willReturn([$m1, $m2]);
        $matRepo->expects($this->exactly(2))->method('Eliminar')->willReturnCallback(static fn (Matricula $m): bool => $m !== $m1);

        $msg = $this->createSut($app, $matRepo)->execute(['id_activ' => 1, 'id_nom' => 2]);

        $this->assertNotSame('', $msg);
    }

    public function test_borra_las_matriculas_de_esa_persona_en_esa_actividad(): void
    {
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($this->asistenteDeMiDl(7, 3));
        $app->method('eliminar')->willReturn(true);
        $m = new Matricula();
        $matRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $matRepo->method('matching')->willReturn([$m]);
        $matRepo->expects($this->once())->method('Eliminar')->with($m)->willReturn(true);

        $this->assertSame('', $this->createSut($app, $matRepo)->execute(['id_activ' => '7', 'id_nom' => '3']));
    }

    public function test_desde_el_dossier_de_la_actividad_la_persona_va_en_sel_y_la_actividad_en_id_pau(): void
    {
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->expects($this->once())->method('findById')->with(7, 3)->willReturn($this->asistenteDeMiDl(7, 3));
        $app->method('eliminar')->willReturn(true);

        $this->assertSame('', $this->createSut($app)->execute(['pau' => 'a', 'sel' => ['3#x'], 'id_pau' => '7']));
    }

    public function test_cierra_el_dossier_1301_de_la_persona_si_lo_tiene(): void
    {
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($this->asistenteDeMiDl(7, 3));
        $app->method('eliminar')->willReturn(true);
        $dossier = $this->createMock(\src\dossiers\domain\entity\Dossier::class);
        $dossier->expects($this->once())->method('cerrar');
        $dosRepo = $this->createMock(DossierRepositoryInterface::class);
        $dosRepo->method('findByPk')->with($this->callback(
            static fn ($pk): bool => $pk == \src\dossiers\domain\value_objects\DossierPk::fromArray(['tabla' => 'p', 'id_pau' => 3, 'id_tipo_dossier' => 1301])
        ))->willReturn($dossier);
        $dosRepo->expects($this->once())->method('Guardar')->with($dossier);

        $this->assertSame('', $this->createSut($app, null, $dosRepo)->execute(['id_activ' => 7, 'id_nom' => 3]));
    }

    /**
     * Hallazgo: el dossier 1301 se cierra aunque la persona siga teniendo otras actividades.
     */
    public function test_el_dossier_se_cierra_sin_mirar_si_quedan_otras_actividades(): void
    {
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($this->asistenteDeMiDl(7, 3));
        $app->expects($this->never())->method('matching');
        $app->method('eliminar')->willReturn(true);
        $dossier = $this->createMock(\src\dossiers\domain\entity\Dossier::class);
        $dossier->expects($this->once())->method('cerrar');
        $dosRepo = $this->createMock(DossierRepositoryInterface::class);
        $dosRepo->method('findByPk')->willReturn($dossier);

        $this->createSut($app, null, $dosRepo)->execute(['id_activ' => 7, 'id_nom' => 3]);
    }

    public function test_si_no_se_borra_el_asistente_no_se_toca_ni_el_dossier_ni_las_matriculas(): void
    {
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($this->asistenteDeMiDl(7, 3));
        $app->method('eliminar')->willReturn(false);
        $matRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $matRepo->expects($this->never())->method('matching');
        $dosRepo = $this->createMock(DossierRepositoryInterface::class);
        $dosRepo->expects($this->never())->method('findByPk');

        $this->assertNotSame('', $this->createSut($app, $matRepo, $dosRepo)->execute(['id_activ' => 7, 'id_nom' => 3]));
    }

    public function test_un_asistente_de_otra_dl_no_se_borra(): void
    {
        $ajeno = $this->asistenteDeMiDl(7, 3);
        $ajeno->setDl_responsable('dlp');
        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($ajeno);
        $app->expects($this->never())->method('eliminar');

        $this->assertNotSame('', $this->createSut($app)->execute(['id_activ' => 7, 'id_nom' => 3]));
    }
}
