<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\personas\application\PersonaUpdate;
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
use src\personas\domain\entity\PersonaN;
use src\personas\domain\entity\PersonaNax;
use src\personas\domain\entity\PersonaS;
use src\personas\domain\entity\PersonaSSSC;

/** Valores que graba la ficha de una persona, con entidades reales, para cada tipo. */
final class PersonaUpdateValoresTest extends TestCase
{
    private mixed $sesion = null;
    /** @var array<class-string, object> */
    private array $repos = [];
    private ?object $guardada = null;

    protected function setUp(): void
    {
        $this->sesion = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv', 'idioma' => 'es_ES.UTF-8'];
    }

    protected function tearDown(): void
    {
        if ($this->sesion === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->sesion;
        }
    }

    private function uc(): PersonaUpdate
    {
        $ids = [PersonaNRepositoryInterface::class, PersonaAgdRepositoryInterface::class, PersonaNaxRepositoryInterface::class,
            PersonaSRepositoryInterface::class, PersonaSSSCRepositoryInterface::class, PersonaExRepositoryInterface::class,
            PersonaDlRepositoryInterface::class, PersonaSacdRepositoryInterface::class];
        $args = array_map(fn (string $i) => $this->repos[$i] ?? $this->createMock($i), $ids);
        return new PersonaUpdate(new PersonaRepositoryResolver(...$args));
    }

    /** @return array<string, string> */
    private static function formulario(string $objPau): array
    {
        return [
            'id_nom' => '100555', 'obj_pau' => $objPau, 'dl' => 'dlb', 'situacion' => 'A', 'idioma_preferido' => 'ca_ES.UTF-8',
            'nivel_stgr' => '2', 'trato' => 'D.', 'nom' => 'Juan', 'apel_fam' => 'Juanito', 'nx1' => 'de', 'apellido1' => 'García',
            'nx2' => 'y', 'apellido2' => 'López', 'lugar_nacimiento' => 'Barcelona', 'f_nacimiento' => '3/4/1980',
            'f_situacion' => '1/9/2020', 'profesion' => 'ingeniero', 'sacd' => 't', 'eap' => 'eap', 'inc' => 'fl',
            'f_inc' => '2/2/2000', 'observ' => 'obs', 'id_ctr' => '1010810', 'ce' => '3', 'ce_lugar' => 'Roma', 'ce_ini' => '2001', 'ce_fin' => '2004',
        ];
    }

    public static function providerTipos(): \Generator
    {
        yield 'n' => ['PersonaN', PersonaNRepositoryInterface::class, PersonaN::class, 'n'];
        yield 'agd' => ['PersonaAgd', PersonaAgdRepositoryInterface::class, PersonaAgd::class, 'a'];
        yield 'nax' => ['PersonaNax', PersonaNaxRepositoryInterface::class, PersonaNax::class, 'x'];
        yield 's' => ['PersonaS', PersonaSRepositoryInterface::class, PersonaS::class, 's'];
        yield 'sssc' => ['PersonaSSSC', PersonaSSSCRepositoryInterface::class, PersonaSSSC::class, 'sssc'];
    }

    #[DataProvider('providerTipos')]
    public function test_alta_de_cada_tipo_graba_todos_los_campos(string $objPau, string $repoId, string $clase, string $idTabla): void
    {
        $repo = $this->createMock($repoId);
        $repo->method('findById')->willReturn(null);
        $repo->expects($this->once())->method('Guardar')->willReturnCallback(function (object $p): bool {
            $this->guardada = $p;
            return true;
        });
        $this->repos[$repoId] = $repo;

        $this->assertSame('', $this->uc()->execute(self::formulario($objPau)));

        $p = $this->guardada;
        $this->assertInstanceOf($clase, $p);
        $this->assertSame([100555, $idTabla, 'dlb', 'A'], [$p->getId_nom(), $p->getId_tabla(), $p->getDl(), $p->getSituacion()]);
        $this->assertSame(['D.', 'Juan', 'Juanito', 'de', 'García', 'y', 'López'], [$p->getTrato(), $p->getNom(), $p->getApel_fam(), $p->getNx1(), $p->getApellido1(), $p->getNx2(), $p->getApellido2()]);
        $this->assertSame(['1980-04-03', '2020-09-01', '2000-02-02'], [$p->getF_nacimiento()->format('Y-m-d'), $p->getF_situacion()->format('Y-m-d'), $p->getF_inc()->format('Y-m-d')]);
        $this->assertSame([2, 'ca_ES.UTF-8', 'Barcelona', 'ingeniero', true, 'eap', 'fl', 'obs', 1010810], [
            $p->getNivel_stgr(), $p->getIdioma_preferido(), $p->getLugar_nacimiento(), $p->getProfesion(), $p->isSacd(), $p->getEap(), $p->getInc(), $p->getObserv(), $p->getId_ctr(),
        ]);
        if (method_exists($p, 'getCe')) {
            $this->assertSame(['3', 'Roma', '2001', '2004'], [$p->getCe(), $p->getCe_lugar(), $p->getCe_ini(), $p->getCe_fin()]);
        }
    }

    public function test_actualizar_una_persona_existente_conserva_el_tipo_y_vacia_lo_que_llega_vacio(): void
    {
        $existente = new PersonaAgd();
        $existente->setId_nom(100555);
        $existente->setId_tabla('a');
        $existente->setApellido1('Viejo');
        $existente->setId_ctr(1010810);
        $existente->setF_inc(new \src\shared\domain\value_objects\DateTimeLocal('2000-01-01'));
        $repo = $this->createMock(PersonaAgdRepositoryInterface::class);
        $repo->method('findById')->with(100555)->willReturn($existente);
        $repo->expects($this->once())->method('Guardar')->with($this->identicalTo($existente))->willReturn(true);
        $this->repos[PersonaAgdRepositoryInterface::class] = $repo;

        $this->assertSame('', $this->uc()->execute(['id_nom' => '100555', 'obj_pau' => 'PersonaAgd', 'apellido1' => 'Nuevo', 'sacd' => '']));

        $this->assertSame(['a', 'Nuevo', null, null, false, null], [
            $existente->getId_tabla(), $existente->getApellido1(), $existente->getId_ctr(), $existente->getF_inc(), $existente->isSacd(), $existente->getNivel_stgr(),
        ]);
    }

    public function test_fecha_con_formato_no_valido_queda_vacia(): void
    {
        $repo = $this->createMock(PersonaNRepositoryInterface::class);
        $repo->method('findById')->willReturn(null);
        $repo->method('Guardar')->willReturnCallback(function (PersonaN $p): bool {
            $this->guardada = $p;
            return true;
        });
        $this->repos[PersonaNRepositoryInterface::class] = $repo;
        $this->uc()->execute(['f_nacimiento' => 'ayer'] + self::formulario('PersonaN'));
        $this->assertNull($this->guardada->getF_nacimiento());
    }
}
