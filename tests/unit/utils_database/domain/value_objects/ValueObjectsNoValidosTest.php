<?php

declare(strict_types=1);

namespace Tests\unit\utils_database\domain\value_objects;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\utils_database\domain\value_objects\DbSchemaCode;
use src\utils_database\domain\value_objects\DbSchemaId;
use src\utils_database\domain\value_objects\MapIdDl;
use src\utils_database\domain\value_objects\MapIdResto;
use src\utils_database\domain\value_objects\MapObjectCode;

/** Valores no válidos de los VO de `utils_database` (vacío, caracteres, texto no numérico, cero o negativo). */
final class ValueObjectsNoValidosTest extends TestCase
{
    /**
     * @return iterable<string, array{callable(): mixed, string}>
     */
    public static function noValidosProvider(): iterable
    {
        yield 'DbSchemaCode vacío' => [fn() => new DbSchemaCode('   '), 'DbSchemaCode cannot be empty'];
        yield 'DbSchemaCode con espacio' => [fn() => new DbSchemaCode('H dlb'), 'DbSchemaCode has invalid characters'];
        yield 'DbSchemaCode con comilla' => [fn() => new DbSchemaCode('H-dlb"'), 'DbSchemaCode has invalid characters'];
        yield 'MapObjectCode vacío' => [fn() => new MapObjectCode(''), 'MapObjectCode cannot be empty'];
        yield 'MapObjectCode con punto' => [fn() => new MapObjectCode('Casa.Dl'), 'MapObjectCode has invalid characters'];
        yield 'MapIdDl cero' => [fn() => new MapIdDl(0), 'MapIdDl must be a positive integer'];
        yield 'MapIdDl negativo' => [fn() => new MapIdDl(-5), 'MapIdDl must be a positive integer'];
        yield 'MapIdDl texto con signo' => [fn() => MapIdDl::fromString('-5'), 'MapIdDl string must be digits'];
        yield 'MapIdDl texto vacío' => [fn() => MapIdDl::fromString(''), 'MapIdDl string must be digits'];
        yield 'MapIdResto texto no numérico' => [fn() => MapIdResto::fromString('12a'), 'MapIdResto string must be digits'];
        yield 'DbSchemaId texto vacío' => [fn() => DbSchemaId::fromString(' '), 'DbSchemaId string must be a signed decimal integer'];
        yield 'DbSchemaId texto decimal' => [fn() => DbSchemaId::fromString('1.5'), 'DbSchemaId string must be a signed decimal integer'];
    }

    #[DataProvider('noValidosProvider')]
    public function test_valor_no_valido_lanza_excepcion_con_mensaje(callable $crear, string $mensaje): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($mensaje);
        $crear();
    }

    public function test_from_string_validos(): void
    {
        $this->assertSame(12, MapIdDl::fromString('12')->value());
        $this->assertSame('Actividad', MapObjectCode::fromString('Actividad')->value());
        $this->assertSame('Casa_dl-2', (new MapObjectCode('  Casa_dl-2 '))->value());
    }
}
