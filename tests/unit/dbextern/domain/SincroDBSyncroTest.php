<?php

declare(strict_types=1);

namespace Tests\unit\dbextern\domain;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\dbextern\domain\VincularIdMatch;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\entity\DlListas;
use src\dbextern\domain\entity\PersonaBDU;
use src\dbextern\domain\SincroDB;
use src\dbextern\infrastructure\persistence\postgresql\OdbcDlListasRepository;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryFactoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\entity\PersonaSSSC;
use src\personas\domain\entity\TelecoPersona;
use src\personas\domain\Trasladar;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Sincronizar una persona de Orbix con su ficha de la BDU (Listas): datos
 * personales, centro, dl, centro de estudios, incorporación y telecos, para
 * cada tipo de persona según el primer dígito del identificador de Listas.
 */
final class SincroDBSyncroTest extends TestCase
{
    private PersonaNRepositoryInterface&MockObject $repoN;
    private PersonaSSSCRepositoryInterface&MockObject $repoSSSC;
    private TelecoPersonaDlRepositoryInterface&MockObject $telecos;
    /** @var list<object> */
    private array $guardadas = [];
    /** @var list<TelecoPersona> */
    private array $telecosExistentes = [];

    protected function setUp(): void
    {
        $this->repoN = $this->createMock(PersonaNRepositoryInterface::class);
        $this->repoSSSC = $this->createMock(PersonaSSSCRepositoryInterface::class);
        $this->telecos = $this->createMock(TelecoPersonaDlRepositoryInterface::class);
        $this->telecos->method('matching')->willReturnCallback(fn (): array => $this->telecosExistentes);
        $this->telecos->method('getNewId')->willReturn(5001);
        $this->telecos->method('Guardar')->willReturnCallback(function (TelecoPersona $t): bool {
            $this->guardadas[] = $t;
            return true;
        });
    }

    private function sincro(): SincroDB
    {
        $dl = new DlListas();
        $dl->setDl('b');
        $dl->setNombre_dl('Barcelona');
        $cr = new DlListas();
        $cr->setDl('X');
        $cr->setNombre_dl('Región cr Iers');
        $listas = $this->createMock(OdbcDlListasRepository::class);
        $listas->method('getDlListas')->willReturn([$dl, $cr]);
        $resolver = new PersonaRepositoryResolver(
            $this->repoN, $this->createMock(PersonaAgdRepositoryInterface::class), $this->createMock(PersonaNaxRepositoryInterface::class),
            $this->createMock(PersonaSRepositoryInterface::class), $this->repoSSSC, $this->createMock(PersonaExRepositoryInterface::class),
            $this->createMock(PersonaDlRepositoryInterface::class), $this->createMock(PersonaSacdRepositoryInterface::class),
        );
        $s = new SincroDB(
            $this->createMock(PersonaBDURepositoryInterface::class), $this->createMock(IdMatchPersonaRepositoryInterface::class),
            $this->createMock(PersonaDlRepositoryInterface::class), $this->createMock(PersonaDlRepositoryFactoryInterface::class),
            $this->telecos, $listas, $resolver, $this->createMock(Trasladar::class), $this->createMock(VincularIdMatch::class),
        );
        $s->setCentros(['Cadiera' => 1010810]);
        return $s;
    }

    /** @param array<string, mixed> $cambios */
    private function bdu(int $identif, array $cambios = []): PersonaBDU&MockObject
    {
        $datos = $cambios + [
            'getApenom' => 'Gil Paz, Ana', 'getNombre' => 'Ana', 'getNx1' => 'de', 'getApellido1_sinprep' => 'Gil', 'getNx2' => '',
            'getApellido2_sinprep' => 'Paz', 'getFechaNacimientoVo' => new DateTimeLocal('1980-03-15'), 'getLugar_Naci' => 'Madrid',
            'getDl' => 'b', 'getCtr' => 'Cadiera', 'getEmail' => 'ana@x.org', 'getTfno_Movil' => '', 'getCe_num' => '3',
            'getCe_lugar' => 'Roma', 'getCe_ini' => '2001', 'getCe_fin' => '', 'getInc' => 'fl', 'getF_inc' => '2005-2-1',
            'getEncargos' => 'eap', 'getProfesion_cargo' => 'médico',
        ];
        $p = $this->createMock(PersonaBDU::class);
        $p->method('getIdentif')->willReturn($identif);
        foreach ($datos as $m => $v) {
            $p->method($m)->willReturn($v);
        }
        return $p;
    }

    private function numerario(string $situacion = 'A'): PersonaN
    {
        $p = new PersonaN();
        $p->setId_nom(10011);
        $p->setSituacion($situacion);
        return $p;
    }

    public function test_numerario_copia_todos_los_datos_de_la_bdu(): void
    {
        $p = $this->numerario('L');
        $this->repoN->method('findById')->with(10011)->willReturn($p);
        $this->repoN->expects($this->once())->method('Guardar')->with($p)->willReturn(true);

        $this->assertSame('', $this->sincro()->syncro($this->bdu(1000123), 10011));

        $this->assertSame(['A', date('Y-m-d')], [$p->getSituacion(), $p->getF_situacion()?->format('Y-m-d')], 'vuelve a activa');
        $this->assertSame(['Ana', 'de', 'Gil', 'Paz', 'Madrid', '1980-03-15'], [$p->getNom(), $p->getNx1(), $p->getApellido1(), $p->getApellido2(), $p->getLugar_nacimiento(), $p->getF_nacimiento()?->format('Y-m-d')]);
        $this->assertSame(['3', 'Roma', '2001', null], [$p->getCe(), $p->getCe_lugar(), $p->getCe_ini(), $p->getCe_fin()]);
        $this->assertSame(['fl', '2005-02-01', 'médico', 'eap', 'dlb', 1010810], [$p->getInc(), $p->getF_inc()?->format('Y-m-d'), $p->getProfesion(), $p->getEap(), $p->getDl(), $p->getId_ctr()]);
        $this->assertCount(1, $this->guardadas);
        $this->assertSame(['ana@x.org', 'de listas', 3, 13], [$this->guardadas[0]->getNum_teleco(), $this->guardadas[0]->getObserv(), $this->guardadas[0]->getId_tipo_teleco(), $this->guardadas[0]->getId_desc_teleco()]);
    }

    public function test_dl_de_una_region(): void
    {
        $p = $this->numerario();
        $this->repoN->method('findById')->willReturn($p);
        $this->repoN->method('Guardar')->willReturn(true);
        $this->sincro()->syncro($this->bdu(1000123, ['getDl' => 'X']), 10011);
        $this->assertSame('crIers', $p->getDl());
    }

    public function test_sin_ctr_o_con_ctr_desconocido_avisa_y_quita_el_centro(): void
    {
        $p = $this->numerario();
        $p->setId_ctr(999);
        $this->repoN->method('findById')->willReturn($p);
        $this->repoN->method('Guardar')->willReturn(true);
        $this->assertStringContainsString('no tiene puesto el ctr', (string) $this->sincro()->syncro($this->bdu(1000123, ['getCtr' => '']), 10011));
        $this->assertSame(0, $p->getId_ctr());
        $this->assertStringContainsString('Otro', (string) $this->sincro()->syncro($this->bdu(1000123, ['getCtr' => 'Otro']), 10011));
    }

    public function test_sss_mas_no_toca_el_centro_de_estudios(): void
    {
        $p = new PersonaSSSC();
        $p->setId_nom(10044);
        $p->setSituacion('A');
        $this->repoSSSC->method('findById')->willReturn($p);
        $this->repoSSSC->expects($this->once())->method('Guardar')->willReturn(true);
        $this->assertSame('', $this->sincro()->syncro($this->bdu(4000123), 10044));
        $this->assertSame('Gil', $p->getApellido1());
    }

    public function test_errores(): void
    {
        $s = $this->sincro();
        $this->assertSame(['error' => 'No existe la clase de la persona'], $s->syncro($this->bdu(9000123), 1));
        $this->repoN->method('findById')->willReturnOnConsecutiveCalls(null, $this->numerario());
        $this->assertSame(['error' => 'No se encontró la persona en Orbix'], $s->syncro($this->bdu(1000123), 1));
        $this->repoN->method('Guardar')->willReturn(false);
        $r = $s->syncro($this->bdu(1000123), 10011);
        $this->assertTrue($r['abort']);
        $this->assertSame([], $this->guardadas, 'sin ficha guardada no se tocan los telecos');
    }

    public function test_telecos_actualiza_los_de_listas_sin_duplicar(): void
    {
        $t = new TelecoPersona();
        $t->setId_item(77);
        $t->setNum_teleco('vieja@x.org');
        $t->setObserv('de listas');
        $this->telecosExistentes = [$t];
        $this->repoN->method('findById')->willReturn($this->numerario());
        $this->repoN->method('Guardar')->willReturn(true);
        $this->sincro()->syncro($this->bdu(1000123), 10011);
        $this->assertSame([$t], $this->guardadas);
        $this->assertSame('ana@x.org', $t->getNum_teleco());
    }
}
