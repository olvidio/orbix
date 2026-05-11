<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\procesos\application\ProcesosDepende;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;

/**
 * Tests unitarios para el caso de uso ProcesosDepende centrados en el
 * camino de retorno temprano, donde no se debe interactuar con la BD.
 *
 * {@see ProcesosDepende::execute} devuelve un array JSON-serializable (`opciones`, `blanco`),
 * no una cadena vacía.
 */
final class ProcesosDependeTest extends TestCase
{
    private mixed $previousContainer;

    private const PAYLOAD_SIN_OPCIONES = [
        'opciones' => [],
        'blanco' => true,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        parent::tearDown();
    }

    #[DataProvider('inputsEarlyReturnProvider')]
    public function test_early_return_sin_bd_devuelve_opciones_vacias(array $input): void
    {
        $out = (new ProcesosDepende())->execute($input);
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

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadTareaRepositoryInterface::class => $repo,
        ]);

        $out = (new ProcesosDepende())->execute([
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

        $GLOBALS['container'] = $this->containerFromMap([
            ActividadTareaRepositoryInterface::class => $repo,
        ]);

        $out = (new ProcesosDepende())->execute([
            'acc' => '#id_tarea_previa',
            'valor_depende' => '3',
        ]);

        $this->assertSame(['2' => 'Prev'], $out['opciones']);
        $this->assertTrue($out['blanco']);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class ($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
