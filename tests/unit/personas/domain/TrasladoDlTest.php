<?php

namespace Tests\unit\personas\domain;

use PHPUnit\Framework\TestCase;
use src\personas\domain\TrasladoDl;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaN;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

class TrasladoDlTest extends TestCase
{
    private mixed $originalContainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalContainer = $GLOBALS['container'] ?? null;
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
        $this->replaceContainerWithDelegaciones(['dlb', 'crGalBel']);
        $traslado = new TrasladoDlSpy();

        $traslado->setDl_persona('crGalBel');
        $traslado->setReg_dl_org('H-dlbv');
        $traslado->setReg_dl_dst('GalBel-crGalBelf');
        $result = $traslado->trasladar();

        $this->assertSame('dlb', $traslado->getDl_org());
        $this->assertFalse($result['success']);
        $this->assertSame([], $traslado->calls);
    }

    public function test_trasladar_ejecuta_flujo_completo_si_comprobar_es_ok(): void
    {
        $this->replaceContainerWithDelegaciones(['dlOrigen', 'dlDestino']);
        $traslado = new TrasladoDlSpy();

        $traslado->setId_nom(123);
        $traslado->setDl_persona('dlOrigen');
        $traslado->setReg_dl_org('H-dlOrigenv');
        $traslado->setReg_dl_dst('H-dlDestinov');
        $traslado->setSituacion('L');

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
        $this->replaceContainerWithDelegaciones(['dlOrigen']);
        $traslado = new TrasladoDlSpy();

        $traslado->setId_nom(123);
        $traslado->setDl_persona('dlOrigen');
        $traslado->setReg_dl_org('H-dlOrigenv');
        $traslado->setReg_dl_dst('H-dlDestinov');
        $traslado->setSituacion('L');

        $result = $traslado->trasladar();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('comprobar:', $result['mensaje']);
        $this->assertSame(['cambiarFichaPersona'], $traslado->calls);
    }

    public function test_trasladar_si_ya_esta_trasladado_no_hace_mas_operaciones(): void
    {
        $this->replaceContainerWithDelegaciones(['dlOrigen', 'dlDestino']);
        $traslado = new TrasladoDlSpy();

        $traslado->setId_nom(123);
        $traslado->setDl_persona('dlDestino');
        $traslado->setReg_dl_org('H-dlOrigenv');
        $traslado->setReg_dl_dst('H-dlDestinov');
        $traslado->setSituacion('L');

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

        $personaDlRepo = new FakePersonaDlRepo($personaDl);
        $personaNOrgRepo = new FakePersonaNRepo($personaN);
        $personaNDstRepo = new FakePersonaNDestinationRepo();
        $orgConn = $this->createMock(\PDO::class);
        $dstConn = $this->createMock(\PDO::class);

        $traslado = new TrasladoDlCopyPersonaSpy(
            $personaDlRepo,
            $personaNOrgRepo,
            $personaNDstRepo,
            $orgConn,
            $dstConn
        );
        $traslado->setId_nom($idNom);
        $traslado->setReg_dl_org('H-dlOrigenv');
        $traslado->setReg_dl_dst('GalBel-crGalBelf');
        $traslado->setF_dl(new DateTimeLocal('2026-03-09'));

        $result = $traslado->copiarPersona();

        $this->assertTrue($result, $traslado->getError() ?? '');
        $this->assertInstanceOf(PersonaN::class, $personaNDstRepo->saved);
        $this->assertSame($idNom, $personaNDstRepo->saved->getId_nom());
        $this->assertSame('crGalBel', $personaNDstRepo->saved->getDl());
        $this->assertSame('A', $personaNDstRepo->saved->getSituacion());
    }

    private function replaceContainerWithDelegaciones(array $delegaciones): void
    {
        $repo = new FakeDelegacionRepository($delegaciones);
        $GLOBALS['container'] = new FakeContainer([DelegacionRepositoryInterface::class => $repo]);
    }
}

class TrasladoDlSpy extends TrasladoDl
{
    public array $calls = [];

    public function comprobarNotas()
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }

    public function cambiarFichaPersona()
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }

    public function copiarPersona()
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }

    public function copiarNotas()
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }

    public function apuntar()
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }

    public function trasladarDossiers()
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }

    public function trasladarDossierCertificados()
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }

    public function copiarAsistencias()
    {
        $this->calls[] = __FUNCTION__;
        return true;
    }
}

class TrasladoDlCopyPersonaSpy extends TrasladoDl
{
    public function __construct(
        private object $personaDlRepo,
        private object $personaOrgRepo,
        private object $personaDstRepo,
        private \PDO $fakeOrgConnection,
        private \PDO $fakeDstConnection,
    ) {}

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
        if (str_contains($repositoryId, 'PersonaNRepositoryInterface')) {
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
