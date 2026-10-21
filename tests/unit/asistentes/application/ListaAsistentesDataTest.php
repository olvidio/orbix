<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asistentes\application\ListaAsistentesData;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\personas\domain\entity\PersonaEx;

/**
 * Las personas de paso (id_nom negativo, `p_de_paso_ex`) llegan como PersonaEx
 * y no deben provocar TypeError al listar los datos de cl.
 */
final class ListaAsistentesDataTest extends TestCase
{
    public function test_datos_persona_acepta_persona_ex(): void
    {
        $useCase = new ListaAsistentesData(
            $this->createMock(ActividadAllRepositoryInterface::class),
            $this->createMock(AsistenteRepositoryInterface::class),
            $this->createMock(ActividadCargoRepositoryInterface::class),
            $this->createMock(CargoRepositoryInterface::class),
        );

        $persona = new PersonaEx();
        $persona->setProfesion('ingeniero');
        $persona->setInc('pa');
        $persona->setEap('a');

        $metodo = new ReflectionMethod($useCase, 'datosPersona');
        $datos = $metodo->invoke($useCase, $persona);

        $this->assertSame('ingeniero', $datos['profesion']);
        $this->assertSame('', $datos['edad']);
        $this->assertSame('pa : ?', $datos['inc_f_inc']);
        $this->assertSame('a', $datos['eap']);
    }

    public function test_datos_persona_ex_sin_datos(): void
    {
        $useCase = new ListaAsistentesData(
            $this->createMock(ActividadAllRepositoryInterface::class),
            $this->createMock(AsistenteRepositoryInterface::class),
            $this->createMock(ActividadCargoRepositoryInterface::class),
            $this->createMock(CargoRepositoryInterface::class),
        );

        $metodo = new ReflectionMethod($useCase, 'datosPersona');
        $datos = $metodo->invoke($useCase, new PersonaEx());

        $this->assertSame('', $datos['profesion']);
        $this->assertSame('', $datos['inc_f_inc']);
        $this->assertSame('?', $datos['eap']);
    }
}
