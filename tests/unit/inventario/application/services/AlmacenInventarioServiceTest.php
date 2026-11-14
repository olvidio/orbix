<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application\services;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\configuracion\domain\entity\ConfigSchema;
use src\inventario\application\AlmacenOpcionesData;
use src\inventario\application\services\AlmacenInventarioService;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\entity\UbiInventario;

/**
 * {@see AlmacenInventarioService} (centro de inventario que hace de almacén, de los parámetros de
 * configuración) y {@see AlmacenOpcionesData} (opciones para elegirlo).
 */
final class AlmacenInventarioServiceTest extends TestCase
{
    private function servicio(?string $valor, ?string &$pedido = null): AlmacenInventarioService
    {
        $config = $this->createMock(ConfigSchemaRepositoryInterface::class);
        $config->method('findById')->willReturnCallback(static function (string $parametro) use ($valor, &$pedido): ?ConfigSchema {
            $pedido = $parametro;
            if ($valor === null) {
                return null;
            }
            $c = new ConfigSchema();
            $c->setParametro($parametro);
            $c->setValor($valor);
            return $c;
        });

        return new AlmacenInventarioService($config);
    }

    public function test_lee_el_parametro_del_esquema(): void
    {
        $pedido = null;

        $this->assertSame(40, $this->servicio('40', $pedido)->idUbi());
        $this->assertSame('inventario_ubi_almacen', $pedido);
        $this->assertSame(40, $this->servicio(' 40 ')->idUbi(), 'con espacios');
    }

    /** @return array<string, array{0: ?string}> */
    public static function valoresSinAlmacen(): array
    {
        return [
            'sin fila' => [null],
            'vacío' => [''],
            'cero' => ['0'],
            'no numérico' => ['dlb'],
            'negativo' => ['-4'],
        ];
    }

    #[DataProvider('valoresSinAlmacen')]
    public function test_sin_valor_valido_no_hay_almacen(?string $valor): void
    {
        $this->assertNull($this->servicio($valor)->idUbi());
    }

    public function test_el_aviso_no_esta_vacio(): void
    {
        $this->assertNotSame('', $this->servicio(null)->avisoSinDefinir());
    }

    public function test_opciones_los_centros_con_lugares_en_el_orden_del_repositorio(): void
    {
        $ubis = $this->createMock(UbiInventarioRepositoryInterface::class);
        $ubis->expects($this->exactly(2))->method('getUbisInventarioLugar')->with(true)->willReturnCallback(static function (): array {
            $ubi = static function (int $id, string $nom): UbiInventario {
                $u = new UbiInventario();
                $u->setId_ubi($id);
                $u->setNom_ubi($nom);
                return $u;
            };
            return [$ubi(1003, 'Almacén'), $ubi(501, 'Casa')];
        });

        $r = (new AlmacenOpcionesData($ubis, $this->servicio('501')))->execute();
        $this->assertSame([['1003', 'Almacén'], ['501', 'Casa']], $r['opciones'], 'pares en el orden del repositorio');
        $this->assertSame('501', $r['selected']);

        $this->assertSame('', (new AlmacenOpcionesData($ubis, $this->servicio(null)))->execute()['selected'], 'sin definir');
    }
}
