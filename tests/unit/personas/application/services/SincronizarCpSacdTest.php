<?php

declare(strict_types=1);

namespace Tests\unit\personas\application\services;

use PHPUnit\Framework\TestCase;
use src\personas\application\services\SincronizarCpSacd;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\value_objects\PersonaApellido1Text;
use src\personas\domain\value_objects\PersonaTablaCode;
use src\personas\domain\value_objects\SituacionCode;
use src\personas\infrastructure\persistence\postgresql\CpSacdContexto;
use src\personas\infrastructure\persistence\postgresql\CpSacdWriter;
use src\ubis\domain\value_objects\DelegacionCode;

/**
 * `CpSacdWriter` no es final, así que se puede mockear directamente: son estas
 * pruebas las que fijan el contrato "upsert si debe copiarse, eliminar si no"
 * sin tocar ninguna base de datos.
 *
 * Se usan entidades reales (PersonaN, PersonaEx) en vez de dobles, porque
 * `CpSacdFila::desdePersona()` depende del `toArrayForDatabase()` real
 * (Hydratable/SerializesPublicadoParaJson) y no arrastran ninguna dependencia
 * de sesión al construirse con setters sueltos.
 */
final class SincronizarCpSacdTest extends TestCase
{
    private function contexto(string $dl): CpSacdContexto
    {
        return new CpSacdContexto($this->createMock(\PDO::class), '', $dl);
    }

    private function numerario(int $id_nom, bool $sacd): PersonaN
    {
        $persona = new PersonaN();
        $persona->setId_auto(1);
        $persona->setId_nom($id_nom);
        $persona->setIdTablaVo(new PersonaTablaCode('n'));
        $persona->setApellido1Vo(new PersonaApellido1Text('Perez'));
        $persona->setSituacionVo(new SituacionCode('A'));
        $persona->setSacd($sacd);

        return $persona;
    }

    private function dePaso(int $id_nom, string $idTabla, string $dl, bool $sacd): PersonaEx
    {
        $persona = new PersonaEx();
        $persona->setId_auto(1);
        $persona->setId_nom($id_nom);
        $persona->setIdTablaVo(new PersonaTablaCode($idTabla));
        $persona->setApellido1Vo(new PersonaApellido1Text('Gomez'));
        $persona->setSituacionVo(new SituacionCode('A'));
        $persona->setDlVo(new DelegacionCode($dl));
        $persona->setSacd($sacd);

        return $persona;
    }

    public function test_numerario_con_sacd_verdadero_hace_upsert_y_nunca_elimina(): void
    {
        $writer = $this->createMock(CpSacdWriter::class);
        $writer->expects($this->once())->method('upsert')->willReturn(true);
        $writer->expects($this->never())->method('eliminar');

        $servicio = new SincronizarCpSacd($writer);
        $persona = $this->numerario(2001, true);

        $this->assertTrue($servicio->sincronizarPersona($persona, $this->contexto('dlb')));
    }

    public function test_numerario_con_sacd_falso_elimina_y_nunca_hace_upsert(): void
    {
        $writer = $this->createMock(CpSacdWriter::class);
        $writer->expects($this->once())->method('eliminar')->with($this->anything(), 2001)->willReturn(true);
        $writer->expects($this->never())->method('upsert');

        $servicio = new SincronizarCpSacd($writer);
        $persona = $this->numerario(2001, false);

        $this->assertTrue($servicio->sincronizarPersona($persona, $this->contexto('dlb')));
    }

    public function test_de_paso_de_otra_dl_elimina(): void
    {
        $writer = $this->createMock(CpSacdWriter::class);
        $writer->expects($this->once())->method('eliminar')->with($this->anything(), 10021)->willReturn(true);
        $writer->expects($this->never())->method('upsert');

        $servicio = new SincronizarCpSacd($writer);
        // sacd=true pero de otra dl: no debe copiarse (se borra si estuviera).
        $persona = $this->dePaso(10021, 'pn', 'dlc', true);

        $this->assertTrue($servicio->sincronizarPersona($persona, $this->contexto('dlb')));
    }

    public function test_de_paso_de_la_dl_propia_hace_upsert(): void
    {
        $writer = $this->createMock(CpSacdWriter::class);
        $writer->expects($this->once())->method('upsert')->willReturn(true);
        $writer->expects($this->never())->method('eliminar');

        $servicio = new SincronizarCpSacd($writer);
        $persona = $this->dePaso(10021, 'pn', 'dlb', true);

        $this->assertTrue($servicio->sincronizarPersona($persona, $this->contexto('dlb')));
    }

    public function test_eliminar_persona_llama_siempre_a_eliminar_con_el_id_nom_correcto(): void
    {
        $writer = $this->createMock(CpSacdWriter::class);
        $writer->expects($this->once())->method('eliminar')->with($this->anything(), 2001)->willReturn(true);
        $writer->expects($this->never())->method('upsert');

        $servicio = new SincronizarCpSacd($writer);
        // sacd=true: si sólo mirase debeCopiarse() haría upsert, pero eliminarPersona()
        // siempre debe borrar, sea cual sea el estado sacd de la persona.
        $persona = $this->numerario(2001, true);

        $this->assertTrue($servicio->eliminarPersona($persona, $this->contexto('dlb')));
    }
}
