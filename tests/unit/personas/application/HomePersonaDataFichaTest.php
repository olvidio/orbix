<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\personas\application\HomePersonaData;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\entity\PersonaPub;
use src\personas\domain\services\TelecoPersonaService;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use Tests\myTest;

/** Cabecera de la ficha de una persona: datos, centro, teléfonos y mails, y avisos. */
final class HomePersonaDataFichaTest extends myTest
{
    private PersonaNRepositoryInterface&MockObject $repoN;
    private PersonaDlRepositoryInterface&MockObject $repoDl;
    private PersonaPubRepositoryInterface&MockObject $pub;
    private CentroDlRepositoryInterface&MockObject $centros;
    private TelecoPersonaService&MockObject $telecos;

    public function setUp(): void
    {
        parent::setUp();
        $this->repoN = $this->createMock(PersonaNRepositoryInterface::class);
        $this->repoDl = $this->createMock(PersonaDlRepositoryInterface::class);
        $this->pub = $this->createMock(PersonaPubRepositoryInterface::class);
        $this->centros = $this->createMock(CentroDlRepositoryInterface::class);
        $this->telecos = $this->createMock(TelecoPersonaService::class);
    }

    private function uc(): HomePersonaData
    {
        $resolver = new PersonaRepositoryResolver(
            $this->repoN, $this->createMock(PersonaAgdRepositoryInterface::class), $this->createMock(PersonaNaxRepositoryInterface::class),
            $this->createMock(PersonaSRepositoryInterface::class), $this->createMock(PersonaSSSCRepositoryInterface::class),
            $this->createMock(PersonaExRepositoryInterface::class), $this->repoDl, $this->createMock(PersonaSacdRepositoryInterface::class),
        );
        return new HomePersonaData($resolver, $this->pub, $this->centros, $this->telecos);
    }

    private function telecos(string $fijo, string $movil, string $mail): void
    {
        $this->telecos->method('getTelecosPorTipo')->willReturnCallback(fn (int $id, string $tipo): string => match ($tipo) {
            'telf' => $fijo,
            'móvil' => $movil,
            default => $mail,
        });
    }

    public function test_ficha_de_un_numerario_con_centro_telefonos_y_mails(): void
    {
        $p = new PersonaN();
        $p->setId_nom(100555);
        $p->setNom('Juan');
        $p->setApellido1('García');
        $p->setDl('dlb');
        $p->setIdTablaVo('n');
        $p->setSituacion('A');
        $p->setF_nacimiento(new DateTimeLocal('1980-04-03'));
        $p->setNivel_stgr(2);
        $p->setId_ctr(1010810);
        $p->setObserv('obs');
        $this->repoN->method('findById')->with(100555)->willReturn($p);
        $c = new CentroDl();
        $c->setNombre_ubi('La Cadiera');
        $this->centros->method('findById')->with(1010810)->willReturn($c);
        $this->telecos('93 1', '600 1', 'j@x.org');

        $out = $this->uc()->execute(['sel' => ['100555#n'], 'obj_pau' => 'PersonaN']);

        $this->assertSame([100555, 'n', 'PersonaN', 'dlb', 'A', 'obs', 'La Cadiera'], [$out['id_nom'], $out['id_tabla'], $out['Qobj_pau'], $out['dl'], $out['situacion'], $out['observ'], $out['ctr']]);
        $this->assertStringContainsString('García', $out['titulo']);
        $this->assertSame((new DateTimeLocal('1980-04-03'))->getFromLocal(), $out['f_nacimiento']);
        $this->assertSame('Cuadrienio Año I', $out['stgr']);
        $this->assertSame(['93 1 / 600 1', 'j@x.org'], [$out['telfs'], $out['mails']]);
        $this->assertArrayNotHasKey('aviso', $out);
    }

    public function test_un_solo_tipo_de_telefono_sin_separador_y_persona_sin_centro(): void
    {
        $p = new PersonaN();
        $p->setId_nom(100555);
        $p->setApellido1('García');
        $p->setIdTablaVo('n');
        $p->setSituacion('A');
        $this->repoN->method('findById')->willReturn($p);
        $this->centros->expects($this->never())->method('findById');
        $this->telecos('', '600 1', '');
        $out = $this->uc()->execute(['id_nom' => '100555', 'obj_pau' => 'PersonaN']);
        $this->assertSame(['600 1', '', '', ''], [$out['telfs'], $out['ctr'], $out['stgr'], $out['f_nacimiento']]);
    }

    public function test_persona_dl_generica_pasa_a_su_tipo_real(): void
    {
        $p = new PersonaDl();
        $p->setId_nom(100555);
        $p->setApellido1('García');
        $p->setIdTablaVo('a');
        $p->setSituacion('A');
        $this->repoDl->method('findById')->willReturn($p);
        $this->telecos('', '', '');
        $this->assertSame('PersonaAgd', $this->uc()->execute(['id_nom' => '100555', 'obj_pau' => 'PersonaDl'])['Qobj_pau']);
    }

    public function test_persona_de_paso_desde_publicadas_con_aviso_de_region(): void
    {
        $p = new PersonaPub();
        $p->setId_nom(-5);
        $p->setApellido1('De paso');
        $p->setIdTablaVo('pn');
        $p->setSituacion('A');
        $this->pub->method('findByIdParaListado')->willReturnCallback(function (int $id, array &$problemas, bool &$marca) use ($p): PersonaPub {
            $problemas['region']['dlz'] = 'dlz';
            return $p;
        });
        $this->telecos('', '', '');
        $this->centros->expects($this->never())->method('findById');
        $out = $this->uc()->execute(['id_nom' => '-5', 'obj_pau' => 'PersonaEx']);
        $this->assertSame('PersonaEx', $out['Qobj_pau']);
        $this->assertArrayHasKey('aviso', $out);
    }

    public function test_errores(): void
    {
        $this->assertSame(['error' => 'No existe la clase de la persona'], $this->uc()->execute(['id_nom' => '1', 'obj_pau' => 'Otra']));
        $this->assertSame(['error' => 'No se encuentra la persona'], $this->uc()->execute(['id_nom' => '1', 'obj_pau' => 'PersonaN']));
        $this->assertArrayHasKey('aviso', $this->uc()->execute(['id_nom' => '0', 'obj_pau' => 'PersonaN']));
    }
}
