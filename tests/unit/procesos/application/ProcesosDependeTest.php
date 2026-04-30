<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\procesos\application\ProcesosDepende;

/**
 * Tests unitarios para el caso de uso ProcesosDepende centrados en el
 * camino de retorno temprano, donde no se debe interactuar con la BD.
 *
 * {@see ProcesosDepende::execute} devuelve un array JSON-serializable (`opciones`, `blanco`),
 * no una cadena vacía.
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
}
