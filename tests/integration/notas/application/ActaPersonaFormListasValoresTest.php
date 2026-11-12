<?php

declare(strict_types=1);

namespace Tests\integration\notas\application;

use src\notas\application\support\ActaPersonaFormListas;
use src\notas\domain\value_objects\TipoActa;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use Tests\myTest;

/**
 * `ActaPersonaFormListas::listas()` y `valoresDesdeActa()`: siglas que se ofrecen en el formulario de nota y cómo
 * se reparte un número de acta guardado entre «sigla» y «número» al abrir el formulario.
 *
 * Usa la lista real de esquemas de la BD (`comun`: Galbel-crGalbel, H-H, H-dlb, H-dlmE, H-dlp, Iers-crIers) y
 * delegaciones simuladas.
 */
final class ActaPersonaFormListasValoresTest extends myTest
{
    /** @var array<string, mixed> */
    private array $authAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->authAnterior = $_SESSION['session_auth'] ?? [];
        $_SESSION['session_auth'] = array_merge($this->authAnterior, ['esquema' => 'H-dlbv', 'sfsv' => 1]);
    }

    public function tearDown(): void
    {
        $_SESSION['session_auth'] = $this->authAnterior;
        parent::tearDown();
    }

    private static function delegacion(string $dl, string $region, bool $activa = true): Delegacion
    {
        $d = new Delegacion();
        $d->setDl($dl);
        $d->setRegion($region);
        $d->setActive($activa);

        return $d;
    }

    private function listas(): ActaPersonaFormListas
    {
        $repo = $this->createStub(DelegacionRepositoryInterface::class);
        $repo->method('matching')->willReturn([
            self::delegacion('dlb', 'H'),
            self::delegacion('dlmE', 'H'),
            self::delegacion('crGalbel', 'Galbel'),
            self::delegacion('crB', 'B'),
            self::delegacion('crAfom', 'Afom'),
            self::delegacion('dlz', 'Z'),
            self::delegacion('crViejo', 'V', false),
            self::delegacion('Otra', 'O'),
            self::delegacion('cg', 'cg'),
        ]);

        return new ActaPersonaFormListas($repo);
    }

    public function test_listas_ofrecen_como_certificado_las_regiones_sin_esquema(): void
    {
        $listas = $this->listas()->listas();

        $this->assertSame('dlb', $listas['mi_sigla']);
        $this->assertSame(['dlb'], $listas['siglas_acta_permitidas']);
        $this->assertSame(
            ['Afom' => 'Afom', 'B' => 'B', 'cg' => 'cg', 'crs+' => 'crs+'],
            $listas['opciones_certificado_dl'],
            'sin las que tienen esquema (crGalbel, dlmE), ni la propia, ni inactivas, ni «Otra», ni delegaciones sin esquema'
        );
        $this->assertTrue($this->listas()->siglaPermitidaEnActa('dlb'));
        $this->assertFalse($this->listas()->siglaPermitidaEnActa('dlmE'));
    }

    /**
     * @return iterable<string, array{string, array{acta_cert_dl_sel: string, acta_cert_num: string}}>
     */
    public static function certificadosProvider(): iterable
    {
        yield 'región con prefijo cr' => ['crB 12/24', ['acta_cert_dl_sel' => 'B', 'acta_cert_num' => '12/24']];
        yield 'región sin prefijo' => ['B 12/24', ['acta_cert_dl_sel' => 'B', 'acta_cert_num' => '12/24']];
        yield 'número sin formato' => ['cg n.º 5', ['acta_cert_dl_sel' => 'cg', 'acta_cert_num' => 'n.º 5']];
        yield 'sigla fija: se reconoce entera' => ['crs+ 3', ['acta_cert_dl_sel' => 'crs+', 'acta_cert_num' => '3']];
        yield 'sigla que no está en la lista' => ['crX 7/24', ['acta_cert_dl_sel' => 'X', 'acta_cert_num' => '7/24']];
        yield 'sin sigla' => ['?', ['acta_cert_dl_sel' => '', 'acta_cert_num' => '?']];
    }

    /**
     * @param array{acta_cert_dl_sel: string, acta_cert_num: string} $esperado
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('certificadosProvider')]
    public function test_certificado_se_reparte_en_sigla_y_numero(string $acta, array $esperado): void
    {
        $valores = $this->listas()->valoresDesdeActa($acta, TipoActa::FORMATO_CERTIFICADO);

        $this->assertSame('dlb', $valores['acta_sigla_sel']);
        $this->assertSame('', $valores['acta_num']);
        $this->assertSame($esperado, ['acta_cert_dl_sel' => $valores['acta_cert_dl_sel'], 'acta_cert_num' => $valores['acta_cert_num']]);
    }

    public function test_acta_se_reparte_en_sigla_y_numero(): void
    {
        $this->assertSame(
            ['acta_sigla_sel' => 'dlb', 'acta_num' => '12/24', 'acta_cert_dl_sel' => '', 'acta_cert_num' => ''],
            $this->listas()->valoresDesdeActa(' dlb 12/24 ', TipoActa::FORMATO_ACTA)
        );
        $this->assertSame('crb', $this->listas()->valoresDesdeActa('crb 3/24', TipoActa::FORMATO_ACTA)['acta_sigla_sel']);
    }

    public function test_sin_acta_propone_mi_sigla(): void
    {
        $this->assertSame(
            ['acta_sigla_sel' => 'dlb', 'acta_num' => '', 'acta_cert_dl_sel' => '', 'acta_cert_num' => ''],
            $this->listas()->valoresDesdeActa(null, TipoActa::FORMATO_CERTIFICADO)
        );
    }
}
