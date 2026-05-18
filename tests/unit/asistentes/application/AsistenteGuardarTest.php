<?php

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\TestCase;
use src\asistentes\application\AsistenteGuardar;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\entity\Asistente;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;

final class AsistenteGuardarTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_mod_no_soportado(): void
    {
        $GLOBALS['container'] = $this->containerFromMap($this->minimalContainer());

        $this->assertNotSame('', AsistenteGuardar::execute(['mod' => 'otro', 'id_activ' => 1, 'id_nom' => 1]));
    }

    public function test_faltan_ids(): void
    {
        $GLOBALS['container'] = $this->containerFromMap($this->minimalContainer());

        $this->assertNotSame('', AsistenteGuardar::execute(['mod' => 'nuevo', 'id_activ' => 0, 'id_nom' => 1]));
        $this->assertNotSame('', AsistenteGuardar::execute(['mod' => 'nuevo', 'id_activ' => 10, 'id_nom' => 0]));
    }

    public function test_nuevo_acepta_ids_negativos_de_paso(): void
    {
        $dossier = $this->createMock(Dossier::class);
        $dossier->expects($this->once())->method('abrir');

        $dosRepo = $this->createMock(DossierRepositoryInterface::class);
        $dosRepo->method('findByPk')->willReturn($dossier);
        $dosRepo->expects($this->once())->method('Guardar')->with($dossier)->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(-2001456, -1001123)->willReturn(null);
        $app->expects($this->once())->method('guardar')->willReturnCallback(function (Asistente $a) {
            return $a->getId_activ() === -2001456 && $a->getId_nom() === -1001123;
        });

        $GLOBALS['container'] = $this->containerFromMap([
            AsistenteApplicationService::class => $app,
            DossierRepositoryInterface::class => $dosRepo,
        ]);

        $this->assertSame('', AsistenteGuardar::execute([
            'mod' => 'nuevo',
            'id_activ' => -2001456,
            'id_nom' => -1001123,
            'plaza' => 1,
        ]));
    }

    public function test_mover_sin_id_activ_old(): void
    {
        $GLOBALS['container'] = $this->containerFromMap($this->minimalContainer());

        $this->assertNotSame('', AsistenteGuardar::execute([
            'mod' => 'mover',
            'id_activ' => 2,
            'id_nom' => 3,
            'id_activ_old' => 0,
        ]));
    }

    public function test_nuevo_abre_dossier_existente_y_guarda(): void
    {
        $dossier = $this->createMock(Dossier::class);
        $dossier->expects($this->once())->method('abrir');

        $dosRepo = $this->createMock(DossierRepositoryInterface::class);
        $dosRepo->method('findByPk')->willReturn($dossier);
        $dosRepo->expects($this->once())->method('Guardar')->with($dossier)->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->with(10, 20)->willReturn(null);
        $app->expects($this->once())->method('guardar')->willReturnCallback(function (Asistente $a) {
            return $a->getId_activ() === 10 && $a->getId_nom() === 20;
        });

        $GLOBALS['container'] = $this->containerFromMap([
            AsistenteApplicationService::class => $app,
            DossierRepositoryInterface::class => $dosRepo,
        ]);

        $this->assertSame('', AsistenteGuardar::execute([
            'mod' => 'nuevo',
            'id_activ' => 10,
            'id_nom' => 20,
            'plaza' => 1,
        ]));
    }

    public function test_editar_sin_permiso(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(false);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($o);
        $app->expects($this->never())->method('guardar');

        $GLOBALS['container'] = $this->containerFromMap([
            AsistenteApplicationService::class => $app,
            DossierRepositoryInterface::class => $this->createMock(DossierRepositoryInterface::class),
        ]);

        $this->assertNotSame('', AsistenteGuardar::execute([
            'mod' => 'editar',
            'id_activ' => 1,
            'id_nom' => 2,
        ]));
    }

    public function test_editar_guarda(): void
    {
        $o = $this->createMock(Asistente::class);
        $o->method('perm_modificar')->willReturn(true);

        $app = $this->createMock(AsistenteApplicationService::class);
        $app->method('findById')->willReturn($o);
        $app->expects($this->once())->method('guardar')->with($o)->willReturn(true);

        $GLOBALS['container'] = $this->containerFromMap([
            AsistenteApplicationService::class => $app,
            DossierRepositoryInterface::class => $this->createMock(DossierRepositoryInterface::class),
        ]);

        $this->assertSame('', AsistenteGuardar::execute([
            'mod' => 'editar',
            'id_activ' => 1,
            'id_nom' => 2,
            'encargo' => 'x',
            'plaza' => 1,
        ]));
    }

    /**
     * @return array<class-string, object>
     */
    private function minimalContainer(): array
    {
        return [
            AsistenteApplicationService::class => $this->createMock(AsistenteApplicationService::class),
            DossierRepositoryInterface::class => $this->createMock(DossierRepositoryInterface::class),
        ];
    }

    private function containerFromMap(array $services): object
    {
        return new class($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
