<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadestudios\application\ActaNotasData;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\entity\Acta;

final class ActaNotasDataTest extends TestCase
{
    /** @var array<string, mixed>|null */
    private ?array $prevSessionAuth = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prevSessionAuth = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = [
            'mi_id_schema' => 1001,
            'esquema' => 'H-dlbvv',
            'sfsv' => 1,
            'idioma' => 'ca',
        ];
    }

    protected function tearDown(): void
    {
        if ($this->prevSessionAuth === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->prevSessionAuth;
        }
        parent::tearDown();
    }

    public function test_filtra_acta_y_permiso_por_id_schema_propio(): void
    {
        $aaRepo = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $aaRepo->method('getActividadAsignaturas')->willReturnCallback(
            function (array $where) {
                $this->assertSame(10, $where['id_activ']);
                $this->assertSame(1101, $where['id_asignatura']);
                $this->assertSame(1001, $where['id_schema']);

                return [$this->actividadAsignatura(1001)];
            }
        );

        $actaPropia = new Acta();
        $actaPropia->setActa('dlbv 1/26');
        $actaRepo = $this->createMock(ActaRepositoryInterface::class);
        $actaRepo->method('getActas')->willReturnCallback(
            function (array $where) use ($actaPropia) {
                $this->assertSame(1001, $where['id_schema']);

                return [$actaPropia];
            }
        );

        $matRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $matRepo->method('getMatriculas')->willReturnCallback(
            function (array $where) {
                $this->assertArrayNotHasKey('id_schema', $where);
                $this->assertSame(10, $where['id_activ']);
                $this->assertSame(1101, $where['id_asignatura']);

                return [];
            }
        );

        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $oActiv = $this->createMock(ActividadAll::class);
        $oActiv->method('getNom_activ')->willReturn('CA prueba');
        $oActiv->method('getDl_org')->willReturn('dlbv');
        $actRepo->method('findById')->with(10)->willReturn($oActiv);

        $out = (new ActaNotasData($aaRepo, $actRepo, $matRepo, $actaRepo))->execute([
            'id_activ' => 10,
            'id_asignatura' => 1101,
            'id_schema' => 1001,
        ]);

        $this->assertSame(3, $out['permiso']);
        $this->assertSame(['dlbv 1/26'], $out['acta_notas_a_actas']);
        $this->assertSame('dlbv 1/26', $out['acta_principal']);
        $this->assertSame('acta', $out['notas']);
        $this->assertArrayHasKey('dlbv 1/26', $out['despl_actas_opciones']);
        $this->assertFalse($out['puede_nueva_convocatoria']);
    }

    public function test_otra_dl_no_mezcla_acta_ni_deja_modificar(): void
    {
        $aaRepo = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $aaRepo->method('getActividadAsignaturas')->willReturnCallback(
            function (array $where) {
                $this->assertSame(2002, $where['id_schema']);

                return [$this->actividadAsignatura(2002)];
            }
        );

        $actaAjena = new Acta();
        $actaAjena->setActa('dlxx 9/26');
        $actaRepo = $this->createMock(ActaRepositoryInterface::class);
        $actaRepo->method('getActas')->willReturnCallback(
            function (array $where) use ($actaAjena) {
                $this->assertSame(2002, $where['id_schema']);

                return [$actaAjena];
            }
        );

        $matRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $matRepo->expects($this->once())->method('getMatriculas')->with(
            $this->callback(static function (array $where): bool {
                return !array_key_exists('id_schema', $where);
            })
        )->willReturn([]);

        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $oActiv = $this->createMock(ActividadAll::class);
        $oActiv->method('getNom_activ')->willReturn('CA prueba');
        $oActiv->method('getDl_org')->willReturn('dlbv');
        $actRepo->method('findById')->willReturn($oActiv);

        $out = (new ActaNotasData($aaRepo, $actRepo, $matRepo, $actaRepo))->execute([
            'id_activ' => 10,
            'id_asignatura' => 1101,
            'id_schema' => 2002,
        ]);

        $this->assertSame(1, $out['permiso']);
        $this->assertSame(['dlxx 9/26'], $out['acta_notas_a_actas']);
        $this->assertNotContains('dlbv 1/26', $out['acta_notas_a_actas']);
    }

    public function test_sin_id_schema_elige_la_fila_de_la_sesion(): void
    {
        $aaRepo = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $aaRepo->method('getActividadAsignaturas')->willReturn([
            $this->actividadAsignatura(2002),
            $this->actividadAsignatura(1001),
        ]);

        $actaPropia = new Acta();
        $actaPropia->setActa('dlbv 1/26');
        $actaRepo = $this->createMock(ActaRepositoryInterface::class);
        $actaRepo->expects($this->once())->method('getActas')->with(
            $this->callback(static function (array $where): bool {
                return ($where['id_schema'] ?? 0) === 1001;
            })
        )->willReturn([$actaPropia]);

        $matRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $matRepo->method('getMatriculas')->willReturn([]);

        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->method('findById')->willReturn(null);

        $out = (new ActaNotasData($aaRepo, $actRepo, $matRepo, $actaRepo))->execute([
            'id_activ' => 10,
            'id_asignatura' => 1101,
        ]);

        $this->assertSame(3, $out['permiso']);
        $this->assertSame(['dlbv 1/26'], $out['acta_notas_a_actas']);
    }

    public function test_dl_no_organizadora_filtra_matriculas_por_esquema(): void
    {
        $aaRepo = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $aaRepo->method('getActividadAsignaturas')->willReturn([$this->actividadAsignatura(1001)]);

        $actaRepo = $this->createMock(ActaRepositoryInterface::class);
        $actaRepo->method('getActas')->willReturn([]);

        $matRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $matRepo->expects($this->once())->method('getMatriculas')->with(
            $this->callback(static function (array $where): bool {
                return ($where['id_schema'] ?? 0) === 1001;
            })
        )->willReturn([]);

        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $oActiv = $this->createMock(ActividadAll::class);
        $oActiv->method('getNom_activ')->willReturn('CA ajeno');
        $oActiv->method('getDl_org')->willReturn('dlxx');
        $actRepo->method('findById')->willReturn($oActiv);

        $out = (new ActaNotasData($aaRepo, $actRepo, $matRepo, $actaRepo))->execute([
            'id_activ' => 10,
            'id_asignatura' => 1101,
            'id_schema' => 1001,
        ]);

        $this->assertSame(3, $out['permiso']);
        $this->assertSame(0, $out['matriculados']);
    }

    public function test_acta_firmada_no_sale_en_desplegable_de_edicion(): void
    {
        $aaRepo = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $aaRepo->method('getActividadAsignaturas')->willReturn([$this->actividadAsignatura(1001)]);

        $actaFirmada = new Acta();
        $actaFirmada->setActa('dlbv 1/26');
        $actaFirmada->setPdf('%PDF-fake');
        $actaRepo = $this->createMock(ActaRepositoryInterface::class);
        $actaRepo->method('getActas')->willReturn([$actaFirmada]);

        $matRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $matRepo->method('getMatriculas')->willReturn([]);

        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $oActiv = $this->createMock(ActividadAll::class);
        $oActiv->method('getNom_activ')->willReturn('CA prueba');
        $oActiv->method('getDl_org')->willReturn('dlbv');
        $actRepo->method('findById')->with(10)->willReturn($oActiv);

        $out = (new ActaNotasData($aaRepo, $actRepo, $matRepo, $actaRepo))->execute([
            'id_activ' => 10,
            'id_asignatura' => 1101,
            'id_schema' => 1001,
        ]);

        $this->assertSame(['dlbv 1/26'], $out['acta_notas_a_actas']);
        $this->assertArrayNotHasKey('dlbv 1/26', $out['despl_actas_opciones']);
        $this->assertSame('', $out['acta_asignable']);
        $this->assertFalse($out['hay_alumnos_sin_nota']);
        $this->assertFalse($out['puede_nueva_convocatoria']);
    }

    private function actividadAsignatura(int $idSchema): ActividadAsignatura
    {
        $o = new ActividadAsignatura();
        $o->setId_schema($idSchema);
        $o->setId_activ(10);
        $o->setIdAsignaturaVo(1101);

        return $o;
    }
}
