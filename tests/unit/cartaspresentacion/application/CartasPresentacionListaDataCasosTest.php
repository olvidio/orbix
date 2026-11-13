<?php

declare(strict_types=1);

namespace Tests\unit\cartaspresentacion\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\cartaspresentacion\application\CartasPresentacionListaData;
use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\cartaspresentacion\domain\entity\CartaPresentacion;
use src\ubis\application\services\UbiTelecoService;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrRepositoryInterface;
use src\ubis\domain\entity\Centro;
use src\ubis\domain\entity\Direccion;
use src\ubis\domain\entity\TelecoUbi;
use Tests\myTest;
use Tests\support\DescribeCriteria;
use Tests\unit\ubis\support\UbiResolverFactory;

/**
 * Listado de cartas de presentación ({@see CartasPresentacionListaData}): modos `lista_dl`,
 * `lista_todo` y `get` (por población, país —con la zona de la carta—, región y dl), agrupación
 * por tipo de labor / dl / población / edad, direcciones añadidas (segunda principal), teléfonos,
 * correo de las r de fuera, centros con el tipo de labor sin poner y pintado de la zona y el país.
 */
final class CartasPresentacionListaDataCasosTest extends myTest
{
    private const N = 256;
    private const AGD = 128;
    private const JOVENES = 2;
    private const MAYORES = 1;

    private mixed $sesion;
    private CartaPresentacionDlRepositoryInterface&MockObject $cartasDl;
    private CartaPresentacionRepositoryInterface&MockObject $cartas;
    private CentroRepositoryInterface&MockObject $centros;
    private DireccionCentroRepositoryInterface&MockObject $direcciones;
    private RelacionCentroDireccionRepositoryInterface&MockObject $relaciones;
    private TelecoCtrRepositoryInterface&MockObject $telecos;
    /** @var array<int, Centro> */
    private array $centrosPorId = [];
    /** @var array<int, Direccion> */
    private array $direccionesPorId = [];
    /** @var array<int, array<int, string>> id_ubi => id_tipo_teleco => número */
    private array $telecosPorUbi = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->sesion = $_SESSION['session_auth'] ?? [];
        $_SESSION['session_auth']['esquema'] = 'H-dlbv';
        $_SESSION['session_auth']['sfsv'] = 1;
        $this->cartasDl = $this->createMock(CartaPresentacionDlRepositoryInterface::class);
        $this->cartas = $this->createMock(CartaPresentacionRepositoryInterface::class);
        $this->centros = $this->createMock(CentroRepositoryInterface::class);
        $this->direcciones = $this->createMock(DireccionCentroRepositoryInterface::class);
        $this->relaciones = $this->createMock(RelacionCentroDireccionRepositoryInterface::class);
        $this->telecos = $this->createMock(TelecoCtrRepositoryInterface::class);

        $this->centros->method('findById')->willReturnCallback(fn (int $id): ?Centro => $this->centrosPorId[$id] ?? null);
        $this->direcciones->method('findById')->willReturnCallback(fn (int $id): ?Direccion => $this->direccionesPorId[$id] ?? null);
        $this->relaciones->method('getDireccionesPorUbi')->willReturn([]);
        $this->telecos->method('matching')->willReturnCallback(function ($c): array {
            $f = DescribeCriteria::filtros($c);
            $idUbi = (int) substr($f[0], strlen('id_ubi eq '));
            $tipo = (int) substr($f[1], strlen('id_tipo_teleco eq '));
            $num = $this->telecosPorUbi[$idUbi][$tipo] ?? null;
            if ($num === null) {
                return [];
            }
            $t = new TelecoUbi();
            $t->setNumTelecoVo($num);
            return [$t];
        });
    }

    public function tearDown(): void
    {
        $_SESSION['session_auth'] = $this->sesion;
        parent::tearDown();
    }

    private function uc(): CartasPresentacionListaData
    {
        $teleco = new UbiTelecoService(
            UbiResolverFactory::resolver($this, [TelecoCtrRepositoryInterface::class => $this->telecos]),
            $this->createMock(DescTelecoRepositoryInterface::class),
        );

        return new CartasPresentacionListaData($this->cartasDl, $this->cartas, $this->centros, $this->direcciones, $this->relaciones, $teleco);
    }

    private function centro(int $id, string $nombre, string $dl, ?int $labor, string $tipoCtr = 'n', string $region = 'H', bool $activo = true): Centro
    {
        $c = new Centro();
        $c->setId_ubi($id);
        $c->setNombre_ubi($nombre);
        $c->setDl($dl);
        $c->setRegion($region);
        $c->setTipo_ctr($tipoCtr);
        $c->setTipo_labor($labor);
        $c->setActive($activo);
        $this->centrosPorId[$id] = $c;
        return $c;
    }

    private function direccion(int $id, string $calle, string $poblacion, string $pais = '', string $cp = '08001'): Direccion
    {
        $d = new Direccion();
        $d->setId_direccion($id);
        $d->setDireccion($calle);
        $d->setPoblacion($poblacion);
        $d->setPais($pais);
        $d->setC_p($cp);
        $this->direccionesPorId[$id] = $d;
        return $d;
    }

    private function carta(int $idUbi, int $idDireccion, string $nombre, ?string $zona = null): CartaPresentacion
    {
        $c = new CartaPresentacion();
        $c->setId_ubi($idUbi);
        $c->setId_direccion($idDireccion);
        $c->setPres_nom($nombre);
        $c->setPres_telf('934567890');
        $c->setPres_mail("$nombre@example.org");
        $c->setZona($zona);
        return $c;
    }

    public function test_lista_dl_solo_las_cartas_de_los_centros_de_mi_dl_agrupadas(): void
    {
        $this->centro(1, 'Centro Bcn', 'dlb', self::N | self::JOVENES);
        $this->centro(2, 'Centro Pamplona', 'dlp', self::N | self::JOVENES);
        $this->direccion(100011, 'C/ Mayor 1', 'Barcelona');
        $this->direccion(100012, 'C/ Estafeta 2', 'Pamplona');
        $this->cartasDl->method('matching')->willReturn([$this->carta(1, 100011, 'Pepe'), $this->carta(2, 100012, 'Juan')]);

        $r = $this->uc()->execute(['que' => 'lista_dl']);

        $this->assertSame('', $r['html_errores']);
        $html = $r['html_lista'];
        $this->assertStringContainsString('<h3>Cartas de presentación de n</h3>', $html);
        $this->assertStringContainsString('<h3>dlb - n</h3>', $html);
        $this->assertStringContainsString('BARCELONA', $html);
        $this->assertStringContainsString('Pepe<br>C/ Mayor 1<br>08001 Barcelona', $html);
        $this->assertStringContainsString('934 567 890', $html, 'teléfono formateado');
        $this->assertStringContainsString('>jóvenes</td>', $html);
        $this->assertStringNotContainsString('Juan', $html, 'otra dl');
    }

    public function test_lista_todo_ordena_por_tipo_y_dl_y_las_dl_y_cr_salen_tambien_como_otras_r(): void
    {
        $this->centro(1, 'Centro Bcn', 'dlb', self::AGD | self::MAYORES);
        $this->centro(2, 'Delegación', 'dlp', self::N, 'dl');
        $this->direccion(100011, 'C/ Mayor 1', 'Barcelona');
        $this->direccion(100012, 'C/ Estafeta 2', 'Pamplona');
        $this->cartas->method('matching')->willReturn([$this->carta(2, 100012, 'Juan'), $this->carta(1, 100011, 'Pepe')]);

        $html = $this->uc()->execute(['que' => 'lista_todo'])['html_lista'];

        $this->assertStringContainsString('<h3>dlb - agd</h3>', $html);
        $this->assertStringContainsString('<h3>dlp - n</h3>', $html);
        $this->assertStringContainsString('<h3>dlp - otras r</h3>', $html);
        $posAgd = strpos($html, 'Cartas de presentación de agd');
        $posN = strpos($html, 'Cartas de presentación de n');
        $this->assertNotFalse($posAgd);
        $this->assertLessThan($posN, $posAgd, 'tipos en orden alfabético');
        $this->assertStringContainsString('>mayores</td>', $html);
    }

    /** Regresión del escapado del paso 2.10: la zona y el país se unían con `<br>` y se veía el `<br>` literal. */
    public function test_la_zona_va_bajo_la_edad_y_el_pais_bajo_la_poblacion_escapados_y_con_salto_de_linea(): void
    {
        $this->centro(1, 'Centro', 'dlb', self::N | self::JOVENES);
        $this->direccion(100011, 'Rua 1', 'Lisboa', 'Portugal');
        $this->cartas->method('matching')->willReturn([$this->carta(1, 100011, 'Pepe', 'Gràcia <b>norte</b>')]);

        $html = $this->uc()->execute(['que' => 'lista_todo'])['html_lista'];

        $this->assertStringContainsString('>jóvenes<br>Gràcia &lt;b&gt;norte&lt;/b&gt;</td>', $html);
        $this->assertStringContainsString('>LISBOA<br>(PORTUGAL)</td>', $html);
        $this->assertStringNotContainsString('&lt;br&gt;', $html);
    }

    public function test_centro_sin_tipo_de_labor_va_a_la_lista_de_errores(): void
    {
        $this->centro(1, 'Centro sin labor', 'dlb', null);
        $this->direccion(100011, 'C/ Mayor 1', 'Barcelona');
        $this->cartas->method('matching')->willReturn([$this->carta(1, 100011, 'Pepe')]);

        $r = $this->uc()->execute(['que' => 'lista_todo']);

        $this->assertSame('', $r['html_lista']);
        $this->assertStringContainsString("Centros con el campo 'tipo labor' mal puesto:", $r['html_errores']);
        $this->assertStringContainsString('Centro sin labor', $r['html_errores']);
    }

    public function test_get_por_poblacion_busca_direcciones_y_la_zona_de_las_cartas_y_salta_centros_inactivos(): void
    {
        $this->centro(1, 'Activo', 'dlb', self::N);
        $this->centro(2, 'Inactivo', 'dlb', self::N, 'n', 'H', false);
        $this->centro(3, 'Por zona', 'dlb', self::N);
        $this->direccion(100011, 'C/ Mayor 1', 'Sabadell');
        $this->direccion(100013, 'C/ Otra 3', 'Terrassa');
        $criterios = [];
        $this->direcciones->method('matching')->willReturnCallback(function ($c) use (&$criterios): array {
            $criterios[] = DescribeCriteria::filtros($c);
            return [$this->direccionesPorId[100011]];
        });
        $this->relaciones->method('getUbisPorDireccion')->with(100011)->willReturn([['id_ubi' => 1], ['id_ubi' => 2], ['id_ubi' => 'x']]);
        $this->cartas->method('findById')->willReturnCallback(fn (int $u, int $d): ?CartaPresentacion => $u === 1 ? $this->carta(1, 100011, 'Pepe') : null);
        $this->cartas->method('matching')->willReturnCallback(function ($c): array {
            $this->assertSame(['zona matches_unaccented Sabadell'], DescribeCriteria::filtros($c));
            return [$this->carta(3, 100013, 'Luis', 'Sabadell')];
        });

        $html = $this->uc()->execute(['que' => 'get', 'poblacion' => 'Sabadell', 'pais' => 'España'])['html_lista'];

        $this->assertSame([['poblacion matches_unaccented Sabadell', 'pais matches_unaccented España']], $criterios);
        $this->assertStringContainsString('Pepe', $html);
        $this->assertStringContainsString('Luis', $html, 'por la zona de la carta');
        $this->assertStringNotContainsString('Inactivo', $html);
    }

    public function test_get_por_region_y_dl_busca_los_centros_y_sus_cartas(): void
    {
        $this->centro(1, 'De la región', 'crX', self::N, 'n', 'X');
        $this->centro(2, 'De la dl', 'dlp', self::AGD);
        $this->direccion(100011, 'Calle 1', 'París');
        $this->direccion(100012, 'Calle 2', 'Pamplona');
        $this->centros->method('matching')->willReturnCallback(fn ($c): array => match (DescribeCriteria::filtros($c)) {
            ['region eq X'] => [$this->centrosPorId[1]],
            ['dl eq dlp'] => [$this->centrosPorId[2]],
            default => [],
        });
        $this->cartas->method('matching')->willReturnCallback(fn ($c): array => match (DescribeCriteria::filtros($c)) {
            ['id_ubi eq 1'] => [$this->carta(1, 100011, 'Pierre')],
            ['id_ubi eq 2'] => [$this->carta(2, 100012, 'Juan')],
            default => [],
        });

        $html = $this->uc()->execute(['que' => 'get', 'region' => 'X', 'dl' => 'dlp'])['html_lista'];

        $this->assertStringContainsString('Pierre', $html);
        $this->assertStringContainsString('Juan', $html);
    }

    public function test_telefono_fax_correo_de_una_r_de_fuera_y_segunda_direccion_principal(): void
    {
        $this->centro(1, 'Comisión', 'crX', self::N, 'cr', 'X');
        $this->direccion(100011, 'Calle 1', 'Roma');
        $this->direccion(100014, 'Calle secundaria', 'Roma', '', '00100');
        $this->telecosPorUbi[1] = [1 => '061234567', 4 => '069876543', 3 => 'cr@example.org'];
        $this->relaciones = $this->createMock(RelacionCentroDireccionRepositoryInterface::class);
        $this->relaciones->method('getDireccionesPorUbi')->willReturn([
            ['id_direccion' => 100011, 'principal' => true],
            ['id_direccion' => 100014, 'principal' => true],
            ['id_direccion' => 100099, 'principal' => false],
        ]);
        $this->cartas->method('matching')->willReturn([$this->carta(1, 100011, 'Pietro')]);

        $html = $this->uc()->execute(['que' => 'lista_todo'])['html_lista'];

        $this->assertStringContainsString('061 234 567 fax:069 876 543', $html);
        $this->assertStringContainsString('Pietro@example.org / mail casa: cr@example.org', $html, 'separado del correo de contacto');
        $this->assertStringContainsString('<br>---<br>Calle secundaria<br>00100  Roma', $html);
        $this->assertStringNotContainsString('---<br>Calle 1', $html, 'la propia dirección no se repite');
    }

    public function test_formato_de_telefonos(): void
    {
        $this->assertSame('', CartasPresentacionListaData::formatTelf(null));
        $this->assertSame('934 567 890 / 666 777 888', CartasPresentacionListaData::formatTelf('934567890 / 666-777-888'));
        $this->assertSame('+34 93 456', CartasPresentacionListaData::formatTelf('+34 93 456'), 'lo que no encaja se deja igual');
    }
}
