<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\application\Select_asignaturas_de_una_actividad;
use src\actividadestudios\application\support\ActividadAsignaturaSelToken;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use src\utils_database\domain\entity\DbSchema;

final class SelectAsignaturasDeUnaActividadSelTest extends TestCase
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

    public function test_dos_asignaturas_iguales_de_distinta_dl_tienen_sel_distinto(): void
    {
        $propia = $this->actividadAsignatura(1001);
        $ajena = $this->actividadAsignatura(2002);

        $aaRepo = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $aaRepo->method('getActividadAsignaturas')->willReturn([$propia, $ajena]);

        $asig = $this->createMock(Asignatura::class);
        $asig->method('getNombre_corto')->willReturn('Latín I');
        $asig->method('getCreditos')->willReturn(3.0);
        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('findById')->willReturn($asig);

        $schemaRepo = $this->createMock(DbSchemaRepositoryInterface::class);
        $schemaRepo->method('getDbSchemas')->willReturnCallback(
            function (array $where) {
                $id = (int) ($where['id'] ?? 0);
                $o = $this->createMock(DbSchema::class);
                $o->method('getId')->willReturn($id);
                $o->method('getSchema')->willReturn($id === 1001 ? 'H-dlbvv' : 'H-dlxxv');

                return [$o];
            }
        );

        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);

        $select = new Select_asignaturas_de_una_actividad($aaRepo, $schemaRepo, $asigRepo, $actRepo);
        $select->setId_pau(10);
        $valores = $select->getValores();

        $selPropia = is_string($valores[1]['sel'] ?? null) ? $valores[1]['sel'] : '';
        $selAjena = is_string($valores[2]['sel'] ?? null) ? $valores[2]['sel'] : '';

        $this->assertSame('10#1101#true#1001', $selPropia);
        $this->assertSame('10#1101#false#2002', $selAjena);
        $this->assertNotSame($selPropia, $selAjena);
        $this->assertSame(1001, ActividadAsignaturaSelToken::decode($selPropia)['id_schema']);
        $this->assertSame(2002, ActividadAsignaturaSelToken::decode($selAjena)['id_schema']);
        $this->assertSame('(dlbv) Latín I', $valores[1][1] ?? null);
        $this->assertSame('(dlxx) Latín I', $valores[2][1] ?? null);
    }

    private function actividadAsignatura(int $idSchema): ActividadAsignatura
    {
        $o = new ActividadAsignatura();
        $o->setId_schema($idSchema);
        $o->setId_activ(10);
        $o->setIdAsignaturaVo(1101);
        $o->setId_profesor(null);

        return $o;
    }
}
