<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\procesos\application\ProcesosDepende;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;

/**
 * Tests unitarios para ProcesosDepende (early return sin BD).
 */
final class ProcesosDependeTest extends TestCase
{
    private const PAYLOAD_SIN_OPCIONES = [
        'opciones' => [],
        'blanco' => true,
    ];

    #[DataProvider('inputsEarlyReturnProvider')]
    public function test_early_return_sin_bd_devuelve_opciones_vacias(array $input): void
    {
        $useCase = new ProcesosDepende($this->createMock(ActividadTareaRepositoryInterface::class));
        $out = $useCase->execute($input);
        $this->assertSame(self::PAYLOAD_SIN_OPCIONES, $out);
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function inputsEarlyReturnProvider(): array
    {
        return [
            'acc vacío' => [[]],
            'acc no válido' => [['acc' => '#otro', 'valor_depende' => '5']],
        ];
    }

    public function test_acc_id_tarea_consulta_repositorio(): void
    {
        $repo = $this->createMock(ActividadTareaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getArrayActividadTareas')
            ->with(12)
            ->willReturn(['1' => 'T1']);

        $out = (new ProcesosDepende($repo))->execute([
            'acc' => '#id_tarea',
            'valor_depende' => '12',
        ]);

        $this->assertSame(['1' => 'T1'], $out['opciones']);
        $this->assertTrue($out['blanco']);
    }

    public function test_acc_id_tarea_previa_consulta_repositorio(): void
    {
        $repo = $this->createMock(ActividadTareaRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getArrayActividadTareas')
            ->with(3)
            ->willReturn(['2' => 'Prev']);

        $out = (new ProcesosDepende($repo))->execute([
            'acc' => '#id_tarea_previa',
            'valor_depende' => '3',
        ]);

        $this->assertSame(['2' => 'Prev'], $out['opciones']);
        $this->assertTrue($out['blanco']);
    }
}
