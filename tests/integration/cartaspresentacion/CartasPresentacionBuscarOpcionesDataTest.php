<?php

declare(strict_types=1);

namespace Tests\integration\cartaspresentacion;

use src\cartaspresentacion\application\CartasPresentacionBuscarOpcionesData;
use src\shared\infrastructure\DependencyResolver;
use Tests\myTest;

/**
 * Opciones de la pantalla de búsqueda de cartas de presentación, contra la BD de desarrollo:
 * regiones, países de las direcciones de centros y delegaciones de la región H, con claves de texto,
 * y la configuración del formulario que lleva al listado.
 */
final class CartasPresentacionBuscarOpcionesDataTest extends myTest
{
    public function test_opciones_y_configuracion_del_formulario(): void
    {
        $r = DependencyResolver::get(CartasPresentacionBuscarOpcionesData::class)->execute();

        $this->assertSame(['opciones_region', 'opciones_pais', 'opciones_delegacion', 'paths', 'hash_lista'], array_keys($r));
        $this->assertNotSame([], $r['opciones_region']);
        foreach (['opciones_pais', 'opciones_delegacion'] as $k) {
            foreach ($r[$k] as $clave => $etiqueta) {
                $this->assertIsString($clave, $k);
                $this->assertIsString($etiqueta, $k);
            }
        }
        $this->assertNotSame([], $r['opciones_delegacion'], 'hay delegaciones de la región H');
        $this->assertSame('frontend/cartaspresentacion/controller/cartas_presentacion_lista.php', $r['paths']['lista']);
        $this->assertSame(['que' => 'get'], $r['hash_lista']['campos_hidden']);
        $this->assertSame('que!poblacion!region!pais!dl', $r['hash_lista']['campos_form']);
    }
}
