<?php

namespace Tests\unit\misas\application;

use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\misas\domain\entity\InicialesSacd;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaSacd;
use Tests\myTest;

class InicialesSacdServiceTest extends myTest
{
    public function test_obtener_iniciales_non_positive_returns_de_paso(): void
    {
        $svc = new InicialesSacdService(
            $this->createStub(InicialesSacdRepositoryInterface::class),
            $this->createStub(PersonaSacdRepositoryInterface::class)
        );

        $this->assertSame('de paso', $svc->obtenerIniciales(0));
        $this->assertSame('de paso', $svc->obtenerIniciales(-3));
    }

    public function test_obtener_iniciales_from_repository_row(): void
    {
        $row = new InicialesSacd();
        $row->setIniciales('ABC');

        $inRepo = $this->createStub(InicialesSacdRepositoryInterface::class);
        $inRepo->method('findById')->willReturn($row);

        $svc = new InicialesSacdService(
            $inRepo,
            $this->createStub(PersonaSacdRepositoryInterface::class)
        );

        $this->assertSame('ABC', $svc->obtenerIniciales(100));
    }

    public function test_obtener_iniciales_null_iniciales_returns_placeholder(): void
    {
        $row = new InicialesSacd();
        $row->setIniciales(null);

        $inRepo = $this->createStub(InicialesSacdRepositoryInterface::class);
        $inRepo->method('findById')->willReturn($row);

        $svc = new InicialesSacdService(
            $inRepo,
            $this->createStub(PersonaSacdRepositoryInterface::class)
        );

        $this->assertSame('---', $svc->obtenerIniciales(2));
    }

    public function test_obtener_iniciales_derives_from_persona_when_no_row(): void
    {
        $persona = $this->createStub(PersonaSacd::class);
        $persona->method('getNom')->willReturn('Ana');
        $persona->method('getApellido1')->willReturn('Prueba');
        $persona->method('getApellido2')->willReturn('Dos');

        $inRepo = $this->createStub(InicialesSacdRepositoryInterface::class);
        $inRepo->method('findById')->willReturn(null);

        $pRepo = $this->createStub(PersonaSacdRepositoryInterface::class);
        $pRepo->method('findById')->willReturn($persona);

        $svc = new InicialesSacdService($inRepo, $pRepo);

        $this->assertSame('APD', $svc->obtenerIniciales(55));
    }

    public function test_obtener_iniciales_when_persona_missing(): void
    {
        $inRepo = $this->createStub(InicialesSacdRepositoryInterface::class);
        $inRepo->method('findById')->willReturn(null);

        $pRepo = $this->createStub(PersonaSacdRepositoryInterface::class);
        $pRepo->method('findById')->willReturn(null);

        $svc = new InicialesSacdService($inRepo, $pRepo);

        $this->assertSame('no encuentro a nadie con id_nom: 99', $svc->obtenerIniciales(99));
    }

    public function test_obtener_nombre_con_iniciales_non_positive(): void
    {
        $svc = new InicialesSacdService(
            $this->createStub(InicialesSacdRepositoryInterface::class),
            $this->createStub(PersonaSacdRepositoryInterface::class)
        );

        $this->assertSame('-?-', $svc->obtenerNombreConIniciales(0));
    }

    public function test_obtener_nombre_con_iniciales_formats_with_iniciales(): void
    {
        $persona = $this->createStub(PersonaSacd::class);
        $persona->method('getNombreApellidos')->willReturn('Ana Prueba Dos');
        $persona->method('getNom')->willReturn('Ana');
        $persona->method('getApellido1')->willReturn('Prueba');
        $persona->method('getApellido2')->willReturn('Dos');

        $inRepo = $this->createStub(InicialesSacdRepositoryInterface::class);
        $inRepo->method('findById')->willReturn(null);

        $pRepo = $this->createStub(PersonaSacdRepositoryInterface::class);
        $pRepo->method('findById')->willReturn($persona);

        $svc = new InicialesSacdService($inRepo, $pRepo);

        $this->assertSame(
            'Ana Prueba Dos (APD)',
            $svc->obtenerNombreConIniciales(1)
        );
    }
}
