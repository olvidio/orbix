<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\personas\application\PersonaEliminar;
use src\personas\application\StgrUpdate;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaAgd;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\entity\PersonaNax;
use src\personas\domain\entity\PersonaS;
use src\personas\domain\entity\PersonaSacd;
use src\personas\domain\entity\PersonaSSSC;

/** Borrar una persona (solo de mi dl) y cambiar su nivel stgr, para cada tipo de persona. */
final class PersonaEliminarYStgrTest extends TestCase
{
    private mixed $sesion = null;
    /** @var array<class-string, object> */
    private array $repos = [];

    protected function setUp(): void
    {
        $this->sesion = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
    }

    protected function tearDown(): void
    {
        if ($this->sesion === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->sesion;
        }
    }

    private function resolver(): PersonaRepositoryResolver
    {
        $ids = [PersonaNRepositoryInterface::class, PersonaAgdRepositoryInterface::class, PersonaNaxRepositoryInterface::class,
            PersonaSRepositoryInterface::class, PersonaSSSCRepositoryInterface::class, PersonaExRepositoryInterface::class,
            PersonaDlRepositoryInterface::class, PersonaSacdRepositoryInterface::class];
        return new PersonaRepositoryResolver(...array_map(fn (string $i) => $this->repos[$i] ?? $this->createMock($i), $ids));
    }

    public static function providerTipos(): \Generator
    {
        yield 'n' => ['PersonaN', 'n', PersonaNRepositoryInterface::class, PersonaN::class];
        yield 'agd' => ['PersonaAgd', 'a', PersonaAgdRepositoryInterface::class, PersonaAgd::class];
        yield 'nax' => ['PersonaNax', 'x', PersonaNaxRepositoryInterface::class, PersonaNax::class];
        yield 's' => ['PersonaS', 's', PersonaSRepositoryInterface::class, PersonaS::class];
        yield 'sssc' => ['PersonaSSSC', 'sssc', PersonaSSSCRepositoryInterface::class, PersonaSSSC::class];
        yield 'de paso' => ['PersonaEx', 'pn', PersonaExRepositoryInterface::class, PersonaEx::class];
        yield 'sacd' => ['PersonaSacd', 'cp', PersonaSacdRepositoryInterface::class, PersonaSacd::class];
    }

    private function persona(string $clase, string $dl): object
    {
        $p = new $clase();
        $p->setId_nom(100555);
        $p->setDl($dl);
        return $p;
    }

    #[DataProvider('providerTipos')]
    public function test_borrar_una_persona_de_mi_dl(string $objPau, string $idTabla, string $repoId, string $clase): void
    {
        $p = $this->persona($clase, 'dlb');
        $repo = $this->createMock($repoId);
        $repo->method('findById')->with(100555)->willReturn($p);
        $repo->expects($this->once())->method('Eliminar')->with($p)->willReturn(true);
        $this->repos[$repoId] = $repo;
        $this->assertSame('', (new PersonaEliminar($this->resolver()))->execute(100555, $objPau));
    }

    public function test_no_borra_una_persona_de_otra_dl(): void
    {
        $repo = $this->createMock(PersonaExRepositoryInterface::class);
        $repo->method('findById')->willReturn($this->persona(PersonaEx::class, 'dlp'));
        $repo->expects($this->never())->method('Eliminar');
        $this->repos[PersonaExRepositoryInterface::class] = $repo;
        $this->assertSame('No se ha eliminado, porque no es de mi dl', (new PersonaEliminar($this->resolver()))->execute(100555, 'PersonaEx'));
    }

    public function test_borrar_errores(): void
    {
        $uc = fn () => new PersonaEliminar($this->resolver());
        $this->assertSame('No se ha pasado el id_nom', $uc()->execute(0, 'PersonaN'));
        $this->assertSame('No existe la clase de la persona', $uc()->execute(1, 'PersonaDl'));
        $this->assertSame('No se encuentra la persona', $uc()->execute(1, 'PersonaN'));
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->willReturn($this->persona(PersonaN::class, 'dlb'));
        $repo->method('Eliminar')->willReturn(false);
        $repo->method('getErrorTxt')->willReturn('clave foránea');
        $this->repos[PersonaNRepositoryInterface::class] = $repo;
        $this->assertSame("hay un error, no se ha eliminado\nclave foránea", $uc()->execute(100555, 'PersonaN'));
    }

    #[DataProvider('providerTipos')]
    public function test_nivel_stgr_de_cada_tipo(string $objPau, string $idTabla, string $repoId, string $clase): void
    {
        $p = $this->persona($clase, 'dlb');
        $repo = $this->createMock($repoId);
        $repo->method('findById')->willReturn($p);
        $repo->expects($this->once())->method('Guardar')->with($p)->willReturn(true);
        $this->repos[$repoId] = $repo;
        $this->assertSame('', (new StgrUpdate($this->resolver()))->execute(100555, $idTabla, '4'));
        $this->assertSame(4, $p->getNivel_stgr());
    }

    public function test_nivel_stgr_en_blanco_queda_nulo_y_errores(): void
    {
        $p = $this->persona(PersonaN::class, 'dlb');
        $p->setNivel_stgr(2);
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->willReturnOnConsecutiveCalls($p, $p, null);
        $repo->method('Guardar')->willReturnOnConsecutiveCalls(true, false);
        $repo->method('getErrorTxt')->willReturn('');
        $this->repos[PersonaNRepositoryInterface::class] = $repo;
        $uc = new StgrUpdate($this->resolver());

        $this->assertSame('', $uc->execute(100555, 'n', ''));
        $this->assertNull($p->getNivel_stgr());
        $this->assertSame('hay un error, no se ha guardado', $uc->execute(100555, 'n', 2));
        $this->assertSame('No se encuentra la persona', $uc->execute(100555, 'n', 2));
        $this->assertSame('No existe la clase de la persona', $uc->execute(100555, 'dl', 2));
        $this->assertSame('No existe la clase de la persona', $uc->execute(100555, 'zz', 2));
    }
}
