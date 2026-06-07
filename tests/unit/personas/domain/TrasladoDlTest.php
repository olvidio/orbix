<?php

namespace Tests\unit\personas\domain;

use PHPUnit\Framework\TestCase;
use src\personas\domain\Trasladar;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\value_objects\SituacionCode;
use src\shared\domain\value_objects\DateTimeLocal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

class TrasladoDlTest extends TestCase
{
    private mixed $originalContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalContainer = $GLOBALS['container'] ?? null;
        if (!isset($GLOBALS['oDBR']) || !$GLOBALS['oDBR'] instanceof \PDO) {
            $GLOBALS['oDBR'] = $this->createMock(\PDO::class);
        }
    }

    protected function tearDown(): void
    {
        if ($this->originalContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->originalContainer;
        }
        parent::tearDown();
    }

    public function test_set_reg_dl_extrae_dl_sin_sufijo_vf(): void
    {
        $traslado = $this->makeTrasladarSpy(['dlb', 'crGalbel']);

        $traslado->setDl_persona('crGalbel');
        $traslado->setReg_dl_org('H-dlbv');
        $traslado->setReg_dl_dst('Galbel-crGalbelf');
        $result = $traslado->trasladar();

        $this->assertSame('dlb', $traslado->getDl_org());
        $this->assertFalse($result['success']);
        $this->assertSame([], $traslado->calls);
    }

    public function test_trasladar_ejecuta_flujo_completo_si_comprobar_es_ok(): void
    {
        $traslado = $this->makeTrasladarSpy(['dlOrigen', 'dlDestino']);

        $traslado->setId_nom(123);
        $traslado->setDl_persona('dlOrigen');
        $traslado->setReg_dl_org('H-dlOrigenv');
        $traslado->setReg_dl_dst('H-dlDestinov');
        $traslado->setSituacionVo(SituacionCode::fromNullableString('L'));

        $result = $traslado->trasladar();

        $this->assertTrue($result['success']);
        $this->assertSame(
            [
                'comprobarNotas',
                'cambiarFichaPersona',
                'copiarPersona',
                'copiarNotas',
                'apuntar',
                'trasladarDossiers',
                'trasladarDossierCertificados',
                'copiarAsistencias',
            ],
            $traslado->calls
        );
    }

    public function test_trasladar_si_no_existe_dl_destino_solo_toca_ficha_y_falla(): void
    {
        $traslado = $this->makeTrasladarSpy(['dlOrigen']);

        $traslado->setId_nom(123);
        $traslado->setDl_persona('dlOrigen');
        $traslado->setReg_dl_org('H-dlOrigenv');
        $traslado->setReg_dl_dst('H-dlDestinov');
        $traslado->setSituacionVo(SituacionCode::fromNullableString('L'));

        $result = $traslado->trasladar();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('comprobar:', $result['mensaje']);
        $this->assertSame(['cambiarFichaPersona'], $traslado->calls);
    }

    public function test_trasladar_si_ya_esta_trasladado_no_hace_mas_operaciones(): void
    {
        $traslado = $this->makeTrasladarSpy(['dlOrigen', 'dlDestino']);

        $traslado->setId_nom(123);
        $traslado->setDl_persona('dlDestino');
        $traslado->setReg_dl_org('H-dlOrigenv');
        $traslado->setReg_dl_dst('H-dlDestinov');
        $traslado->setSituacionVo(SituacionCode::fromNullableString('L'));

        $result = $traslado->trasladar();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('comprobar:', $result['mensaje']);
        $this->assertSame([], $traslado->calls);
    }

    public function test_copiar_persona_copia_con_repositorio_tipado_y_entidad_actual(): void
    {
        $idNom = 123;
        $personaDl = PersonaDl::fromArray([
            'id_nom' => $idNom,
            'id_tabla' => 'n',
            'apellido1' => 'Apellido',
            'situacion' => 'A',
        ]);
        $personaN = PersonaN::fromArray([
            'id_schema' => 1,
            'id_nom' => $idNom,
            'id_tabla' => 'n',
            'apellido1' => 'Apellido',
            'situacion' => 'A',
            'dl' => 'dlOrigen',
        ]);
        $personaN->setId_auto(1);

        $personaDlRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $personaDlRepo->method('findById')->willReturn($personaDl);

        $personaNOrgRepo = $this->createMock(PersonaNRepositoryInterface::class);
        $personaNOrgRepo->method('findById')->willReturn($personaN);

        $saved = null;
        $personaNDstRepo = $this->createMock(PersonaNRepositoryInterface::class);
        $personaNDstRepo->expects($this->once())->method('Guardar')->willReturnCallback(
            function (PersonaN $persona) use (&$saved): bool {
                $saved = $persona;
                return true;
            }
        );
        $orgConn = $this->createMock(\PDO::class);
        $dstConn = $this->createMock(\PDO::class);

        $traslado = new TrasladarCopyPersonaSpy(
            $personaDlRepo,
            $personaNOrgRepo,
            $personaNDstRepo,
            $orgConn,
            $dstConn,
            $this->makeDelegacionRepositoryMock(),
            $this->createMock(AsignaturaRepositoryInterface::class),
            $this->createMock(ActividadAllRepositoryInterface::class),
            $this->createMock(TipoDossierRepositoryInterface::class),
            $this->createMock(CertificadoEmitidoRepositoryInterface::class),
            $this->createMock(ConnectionRepositoryFactoryInterface::class),
        );
        $traslado->setId_nom($idNom);
        $traslado->setReg_dl_org('H-dlOrigenv');
        $traslado->setReg_dl_dst('Galbel-crGalbelf');
        $traslado->setF_traslado(new DateTimeLocal('2026-03-09'));

        $result = $traslado->copiarPersona();

        $this->assertTrue($result, $traslado->getError() ?? '');
        $this->assertInstanceOf(PersonaN::class, $saved);
        $this->assertSame($idNom, $saved->getId_nom());
        $this->assertSame('crGalbel', $saved->getDl());
        $this->assertSame('A', $saved->getSituacion());
    }

    private function makeTrasladarSpy(array $delegaciones = []): TrasladarSpy
    {
        $delegacionRepository = $this->createMock(DelegacionRepositoryInterface::class);
        $delegacionRepository->method('getDelegaciones')->willReturn(
            array_map(
                static fn(string $dl): FakeDelegacion => new FakeDelegacion($dl),
                $delegaciones
            )
        );

        return new TrasladarSpy(
            $delegacionRepository,
            $this->createMock(AsignaturaRepositoryInterface::class),
            $this->createMock(ActividadAllRepositoryInterface::class),
            $this->createMock(TipoDossierRepositoryInterface::class),
            $this->createMock(CertificadoEmitidoRepositoryInterface::class),
            $this->createMock(ConnectionRepositoryFactoryInterface::class),
        );
    }

    private function makeDelegacionRepositoryMock(array $delegaciones = []): DelegacionRepositoryInterface
    {
        $delegacionRepository = $this->createMock(DelegacionRepositoryInterface::class);
        $delegacionRepository->method('getDelegaciones')->willReturn(
            array_map(
                static fn(string $dl): FakeDelegacion => new FakeDelegacion($dl),
                $delegaciones
            )
        );

        return $delegacionRepository;
    }
}

class TrasladarSpy extends Trasladar
{
    public array $calls = [];

    public function __construct(
        DelegacionRepositoryInterface $delegacionRepository,
        AsignaturaRepositoryInterface $asignaturaRepository,
        ActividadAllRepositoryInterface $actividadAllRepository,
        TipoDossierRepositoryInterface $tipoDossierRepository,
        CertificadoEmitidoRepositoryInterface $certificadoEmitidoRepository,
        ConnectionRepositoryFactoryInterface $connectionRepositoryFactory,
    ) {
        parent::__construct(
            $delegacionRepository,
            $asignaturaRepository,
            $actividadAllRepository,
            $tipoDossierRepository,
            $certificadoEmitidoRepository,
            $connectionRepositoryFactory,
        );
    }

    public function comprobarNotas(): bool
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }

    public function cambiarFichaPersona(): bool
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }

    public function copiarPersona(): bool
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }

    public function copiarNotas(): bool
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }

    public function apuntar(): bool
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }

    public function trasladarDossiers(): bool
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }

    public function trasladarDossierCertificados(): bool
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }

    public function copiarAsistencias(): bool
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }
}

class TrasladarCopyPersonaSpy extends Trasladar
{
    public function __construct(
        private object $personaDlRepo,
        private object $personaOrgRepo,
        private object $personaDstRepo,
        private \PDO $fakeOrgConnection,
        private \PDO $fakeDstConnection,
        DelegacionRepositoryInterface $delegacionRepository,
        AsignaturaRepositoryInterface $asignaturaRepository,
        ActividadAllRepositoryInterface $actividadAllRepository,
        TipoDossierRepositoryInterface $tipoDossierRepository,
        CertificadoEmitidoRepositoryInterface $certificadoEmitidoRepository,
        ConnectionRepositoryFactoryInterface $connectionRepositoryFactory,
    ) {
        parent::__construct(
            $delegacionRepository,
            $asignaturaRepository,
            $actividadAllRepository,
            $tipoDossierRepository,
            $certificadoEmitidoRepository,
            $connectionRepositoryFactory,
        );
    }

    protected function getOrgConnectionForCopyPersona(): \PDO
    {
        return $this->fakeOrgConnection;
    }

    protected function getDstConnectionForCopyPersona(): \PDO
    {
        return $this->fakeDstConnection;
    }

    protected function schemaExistsInDatabase(\PDO $oDBorg, string $schema): ?bool
    {
        return true;
    }

    protected function repositoryWithConnection(string $repositoryId, \PDO $oDbl, ?\PDO $oDblSelect = null): object
    {
        if ($repositoryId === PersonaNRepositoryInterface::class) {
            if ($oDbl === $this->fakeOrgConnection) {
                return $this->personaOrgRepo;
            }
            return $this->personaDstRepo;
        }
        return $this->personaDlRepo;
    }
}

class FakeContainer
{
    public function __construct(private array $entries)
    {
    }

    public function get(string $id): object
    {
        if (!array_key_exists($id, $this->entries)) {
            throw new \RuntimeException("No entry for '$id'");
        }
        return $this->entries[$id];
    }
}

class FakePersonaDlRepo
{
    public function __construct(private PersonaDl $persona)
    {
    }

    public function findById(int $idNom): ?PersonaDl
    {
        return $this->persona->getId_nom() === $idNom ? $this->persona : null;
    }
}

class FakePersonaNRepo
{
    public function __construct(private PersonaN $persona)
    {
    }

    public function findById(int $idNom): ?PersonaN
    {
        return $this->persona->getId_nom() === $idNom ? $this->persona : null;
    }
}

class FakePersonaNDestinationRepo
{
    public ?PersonaN $saved = null;

    public function Guardar(PersonaN $persona): bool
    {
        $this->saved = $persona;
        return true;
    }
}

class FakeDelegacionRepository
{
    public function __construct(private array $delegaciones)
    {
    }

    public function getDelegaciones(array $filters = []): array
    {
        return array_map(
            static fn(string $dl): FakeDelegacion => new FakeDelegacion($dl),
            $this->delegaciones
        );
    }
}

class FakeDelegacion
{
    public function __construct(private string $dl)
    {
    }

    public function getDlVo(): FakeDlVo
    {
        return new FakeDlVo($this->dl);
    }
}

class FakeDlVo
{
    public function __construct(private string $value)
    {
    }

    public function value(): string
    {
        return $this->value;
    }
}
