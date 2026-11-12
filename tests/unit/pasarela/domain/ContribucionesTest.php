<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\domain;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\ContribucionNoDuerme;
use src\pasarela\domain\ContribucionReserva;
use src\pasarela\domain\entity\PasarelaConfig;

/**
 * Contribución por reserva y por no dormir (parámetros de la pasarela): valor
 * por defecto y excepciones por tipo de actividad, leídos y guardados como JSON
 * en `pasarela_config`.
 */
final class ContribucionesTest extends TestCase
{
    /** @return iterable<string, array{class-string, string, int, string, string}> */
    public static function clasesProvider(): iterable
    {
        yield 'reserva' => [ContribucionReserva::class, 'contribucion_reserva', 0, 'addContribucionReserva', 'delContribucionReserva'];
        yield 'no duerme' => [ContribucionNoDuerme::class, 'contribucion_no_duerme', 85, 'addContribucionNoDuerme', 'delContribucionNoDuerme'];
    }

    /** @param array<string, mixed>|null $json @param list<PasarelaConfig> $guardados */
    private function repo(?array $json, array &$guardados): PasarelaConfigRepositoryInterface
    {
        $config = null;
        if ($json !== null) {
            $config = new PasarelaConfig();
            $config->setJson_valor($json);
        }
        $repo = $this->createMock(PasarelaConfigRepositoryInterface::class);
        $repo->method('findById')->willReturnCallback(static function () use (&$config) {
            return $config;
        });
        $repo->method('Guardar')->willReturnCallback(static function (PasarelaConfig $c) use (&$guardados, &$config): bool {
            $guardados[] = $c;
            $config = $c;
            return true;
        });
        return $repo;
    }

    #[DataProvider('clasesProvider')]
    public function test_sin_configuracion_usa_los_valores_por_defecto(string $clase, string $param, int $default, string $add, string $del): void
    {
        $g = [];
        $c = new $clase($this->repo(null, $g));
        $this->assertSame($default, $c->getDefault());
        $this->assertSame([111000 => 45, 111001 => 63], $c->getExcepciones());
        $this->assertSame([], $g, 'leer no guarda');
    }

    #[DataProvider('clasesProvider')]
    public function test_lee_la_configuracion_guardada_e_ignora_valores_no_numericos(string $clase, string $param, int $default, string $add, string $del): void
    {
        $g = [];
        $c = new $clase($this->repo(['default' => '20', 'excepciones' => ['112000' => '30', '113000' => 'x']], $g));
        $this->assertSame(20, $c->getDefault());
        $this->assertSame([112000 => 30], $c->getExcepciones());

        $c = new $clase($this->repo(['excepciones' => []], $g));
        $this->assertNull($c->getDefault(), 'sin default guardado');
    }

    #[DataProvider('clasesProvider')]
    public function test_anadir_quitar_y_cambiar_el_defecto_guardan(string $clase, string $param, int $default, string $add, string $del): void
    {
        $g = [];
        $c = new $clase($this->repo(null, $g));
        $c->$add(114000, 50);
        $c->$del(111000);
        $c->setDefault(10);
        $this->assertCount(3, $g);
        $ultimo = end($g);
        $this->assertSame($param, $ultimo->getNom_parametro());
        $this->assertEquals(['default' => 10, 'excepciones' => [111001 => 63, 114000 => 50]], $ultimo->getJson_valor(returnArray: true));
        $this->assertSame([111001 => 63, 114000 => 50], (new $clase($this->repo($ultimo->getJson_valor(returnArray: true), $g)))->getExcepciones(), 'se vuelve a leer igual');
    }
}
