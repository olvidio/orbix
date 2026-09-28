<?php

declare(strict_types=1);

namespace Tests\unit\shared\domain;

use DI\Container;
use PHPUnit\Framework\TestCase;
use src\shared\domain\contracts\DatosCrudRepositoryInterface;
use src\shared\domain\contracts\DatosFichaInterface;
use src\shared\domain\DatosCampo;
use src\shared\domain\DatosUpdateRepo;

/**
 * Alta de ficha: un checkbox desmarcado es false, no null.
 * `empty(false)` no debe pisar el booleano antes de setActive (p. ej. asignatura opcional).
 */
final class DatosUpdateRepoNuevoCheckTest extends TestCase
{
    private mixed $containerPrevio = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->containerPrevio = $GLOBALS['container'] ?? null;
        $GLOBALS['container'] = new Container();
    }

    protected function tearDown(): void
    {
        if ($this->containerPrevio === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->containerPrevio;
        }
        parent::tearDown();
    }

    public function test_nuevo_checkbox_desmarcado_pasa_false(): void
    {
        $repo = new DatosUpdateRepoCheckRepoStub();
        $GLOBALS['container']->set(DatosUpdateRepoCheckRepoStub::class, $repo);

        $ficha = new DatosUpdateRepoCheckFichaStub();
        $updater = new DatosUpdateRepo();
        $updater->setFicha($ficha);
        $updater->setCampos(['nombre' => '']);
        $updater->setRepositoryInterface(DatosUpdateRepoCheckRepoStub::class);

        $result = $updater->nuevo();

        $this->assertTrue($result);
        $this->assertFalse($ficha->active);
        $this->assertNull($ficha->nombre);
        $this->assertSame(42, $ficha->id);
        $this->assertSame($ficha, $repo->saved);
    }

    public function test_nuevo_checkbox_marcado_pasa_true(): void
    {
        $repo = new DatosUpdateRepoCheckRepoStub();
        $GLOBALS['container']->set(DatosUpdateRepoCheckRepoStub::class, $repo);

        $ficha = new DatosUpdateRepoCheckFichaStub();
        $updater = new DatosUpdateRepo();
        $updater->setFicha($ficha);
        $updater->setCampos(['active' => 'on', 'nombre' => 'Optativa']);
        $updater->setRepositoryInterface(DatosUpdateRepoCheckRepoStub::class);

        $result = $updater->nuevo();

        $this->assertTrue($result);
        $this->assertTrue($ficha->active);
        $this->assertSame('Optativa', $ficha->nombre);
    }

    public function test_nuevo_conserva_id_informado_sin_secuencia(): void
    {
        $repo = new DatosUpdateRepoCheckRepoStub();
        $repo->getNewIdThrows = true;
        $GLOBALS['container']->set(DatosUpdateRepoCheckRepoStub::class, $repo);

        $ficha = new DatosUpdateRepoCheckFichaStub();
        $updater = new DatosUpdateRepo();
        $updater->setFicha($ficha);
        $updater->setCampos(['id' => '3415', 'active' => 'on', 'nombre' => 'Optativa']);
        $updater->setRepositoryInterface(DatosUpdateRepoCheckRepoStub::class);

        $result = $updater->nuevo();

        $this->assertTrue($result);
        $this->assertSame(3415, $ficha->id);
        $this->assertFalse($repo->getNewIdCalled);
        $this->assertSame($ficha, $repo->saved);
    }
}

final class DatosUpdateRepoCheckFichaStub implements DatosFichaInterface
{
    public bool $active = true;
    public ?string $nombre = 'keep';
    public int $id = 0;

    public function getPrimary_key(): string
    {
        return 'id';
    }

    /**
     * @return list<DatosCampo>
     */
    public function getDatosCampos(): array
    {
        $active = new DatosCampo();
        $active->setNom_camp('active');
        $active->setMetodoSet('setActive');
        $active->setTipo('check');

        $nombre = new DatosCampo();
        $nombre->setNom_camp('nombre');
        $nombre->setMetodoSet('setNombre');
        $nombre->setTipo('texto');

        return [$active, $nombre];
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function setNombre(?string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function setId(int|string $id): void
    {
        $this->id = (int) $id;
    }
}

final class DatosUpdateRepoCheckRepoStub implements DatosCrudRepositoryInterface
{
    public ?object $saved = null;
    public bool $getNewIdThrows = false;
    public bool $getNewIdCalled = false;

    public function findById(mixed $id): ?object
    {
        return null;
    }

    public function Eliminar(object $entity): bool
    {
        return true;
    }

    public function Guardar(object $entity): bool
    {
        $this->saved = $entity;

        return true;
    }

    public function getErrorTxt(): string
    {
        return '';
    }

    public function getNewId(): int
    {
        $this->getNewIdCalled = true;
        if ($this->getNewIdThrows) {
            throw new \RuntimeException('secuencia inexistente');
        }

        return 42;
    }
}
