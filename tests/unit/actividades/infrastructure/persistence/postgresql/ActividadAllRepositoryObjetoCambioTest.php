<?php

declare(strict_types=1);

namespace Tests\unit\actividades\infrastructure\persistence\postgresql;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use src\actividades\infrastructure\persistence\postgresql\PgActividadAllRepository;

final class ActividadAllRepositoryObjetoCambioTest extends TestCase
{
    #[DataProvider('tablasObjetoProvider')]
    public function test_objeto_cambio_por_tabla(string $tabla, string $objetoEsperado): void
    {
        $ref = new ReflectionClass(PgActividadAllRepository::class);
        /** @var PgActividadAllRepository $repo */
        $repo = $ref->newInstanceWithoutConstructor();

        $setNomTabla = $ref->getMethod('setNomTabla');
        $setNomTabla->setAccessible(true);
        $setNomTabla->invoke($repo, $tabla);

        $getObjetoCambio = $ref->getMethod('getObjetoCambio');
        $getObjetoCambio->setAccessible(true);

        $this->assertSame($objetoEsperado, $getObjetoCambio->invoke($repo));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function tablasObjetoProvider(): array
    {
        return [
            'actividad dl' => ['a_actividades_dl', 'ActividadDl'],
            'actividad ex' => ['a_actividades_ex', 'ActividadEx'],
            'actividad all' => ['a_actividades_all', 'Actividad'],
            'actividad pub' => ['a_actividades', 'Actividad'],
        ];
    }
}
