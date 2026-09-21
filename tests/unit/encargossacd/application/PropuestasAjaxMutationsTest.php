<?php

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\application\PropuestasAjaxMutations;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;

final class PropuestasAjaxMutationsTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $previousPost;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousPost = $_POST;
        $_POST = [];
    }

    protected function tearDown(): void
    {
        $_POST = $this->previousPost;
        parent::tearDown();
    }

    public function test_operacion_desconocida_devuelve_error(): void
    {
        $useCase = new PropuestasAjaxMutations(
            $this->createMock(PropuestaEncargoSacdRepositoryInterface::class),
            $this->createMock(PropuestaEncargoSacdHorarioRepositoryInterface::class),
            $this->createMock(PersonaSacdRepositoryInterface::class),
            $this->createMock(EncargoRepositoryInterface::class),
        );

        $out = $useCase->execute('no_existe');

        $this->assertFalse($out['success']);
        $this->assertNotEmpty($out['mensaje']);
    }

    public function test_lista_sacd_returns_unsigned_popup_data(): void
    {
        $_POST = [
            'id_sacd' => '3',
            'id_item' => '4',
            'id_enc' => '5',
            'tipo' => 'titular',
        ];
        $personas = $this->createMock(PersonaSacdRepositoryInterface::class);
        $personas->expects($this->once())
            ->method('getArraySacd')
            ->with("AND id_tabla ~ '^(a|n|sss)$'")
            ->willReturn([3 => 'Sacd de prueba']);
        $useCase = new PropuestasAjaxMutations(
            $this->createMock(PropuestaEncargoSacdRepositoryInterface::class),
            $this->createMock(PropuestaEncargoSacdHorarioRepositoryInterface::class),
            $personas,
            $this->createMock(EncargoRepositoryInterface::class),
        );

        $out = $useCase->execute('lista_sacd');

        $this->assertTrue($out['success']);
        $this->assertSame('lista_sacd', $out['popup']);
        $this->assertSame([3 => 'Sacd de prueba'], $out['opciones']);
        $this->assertSame(3, $out['id_sacd']);
        $this->assertArrayNotHasKey('html', $out);
    }
}
