<?php

declare(strict_types=1);

namespace Tests\unit\cartaspresentacion\application;

use PHPUnit\Framework\TestCase;
use src\cartaspresentacion\application\CartaPresentacionUpdate;
use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;
use src\cartaspresentacion\domain\contracts\CartaPresentacionExRepositoryInterface;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\cartaspresentacion\domain\entity\CartaPresentacion;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\entity\Centro;

/**
 * Guardar la carta de presentación de un centro: una nueva va a la tabla de la
 * dl si el centro es de mi dl o a la de fuera si es un cr; de otra dl no se
 * puede crear; una existente se actualiza donde esté.
 */
final class CartaPresentacionUpdateTest extends TestCase
{
    private mixed $sesion = null;
    /** @var array<string, list<CartaPresentacion>> */
    private array $guardadas = ['general' => [], 'dl' => [], 'ex' => []];

    protected function setUp(): void
    {
        $this->sesion = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
    }

    protected function tearDown(): void
    {
        if ($this->sesion === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->sesion;
        }
    }

    private function uc(?CartaPresentacion $existente, ?Centro $centro, bool $guarda = true): CartaPresentacionUpdate
    {
        $repo = function (string $k, string $interfaz) use ($existente, $guarda) {
            $r = $this->createMock($interfaz);
            $r->method('findById')->willReturn($existente);
            $r->method('matching')->willReturn([]);
            $r->method('Guardar')->willReturnCallback(function (CartaPresentacion $c) use ($k, $guarda): bool {
                $this->guardadas[$k][] = $c;
                return $guarda;
            });
            return $r;
        };
        $centros = $this->createMock(CentroRepositoryInterface::class);
        $centros->method('findById')->willReturn($centro);
        return new CartaPresentacionUpdate(
            $repo('general', CartaPresentacionRepositoryInterface::class),
            $repo('dl', CartaPresentacionDlRepositoryInterface::class),
            $repo('ex', CartaPresentacionExRepositoryInterface::class),
            $centros,
        );
    }

    private function centro(string $dl, string $tipo = 'n'): Centro
    {
        $c = $this->createStub(Centro::class);
        $c->method('getDl')->willReturn($dl);
        $c->method('getTipo_ctr')->willReturn($tipo);
        return $c;
    }

    /** @return array<string, mixed> */
    private static function entrada(): array
    {
        return ['id_ubi' => 1010810, 'id_direccion' => 55, 'pres_nom' => 'Juan', 'pres_telf' => '93', 'pres_mail' => 'j@x.org', 'zona' => 'Z1', 'observ' => 'obs'];
    }

    public function test_nueva_de_un_centro_de_mi_dl_va_a_la_tabla_de_la_dl(): void
    {
        $this->assertSame(['ok' => true, 'mensaje' => ''], $this->uc(null, $this->centro('dlb'))->execute(self::entrada()));
        $c = $this->guardadas['dl'][0];
        $this->assertSame([1010810, 55, 'Juan', '93', 'j@x.org', 'Z1', 'obs'], [$c->getId_ubi(), $c->getId_direccion(), $c->getPres_nom(), $c->getPres_telf(), $c->getPres_mail(), $c->getZona(), $c->getObserv()]);
    }

    public function test_nueva_de_un_cr_va_a_la_de_fuera_y_de_otra_dl_no_se_puede(): void
    {
        $this->uc(null, $this->centro('crIers', 'cr'))->execute(self::entrada());
        $this->assertCount(1, $this->guardadas['ex']);
        $this->assertSame(['ok' => false, 'mensaje' => 'No puede modificar datos de otra dl'], $this->uc(null, $this->centro('dlp'))->execute(self::entrada()));
        $this->assertSame(['ok' => false, 'mensaje' => 'No puede modificar datos de otra dl'], $this->uc(null, null)->execute(self::entrada()), 'centro no encontrado');
    }

    public function test_existente_se_actualiza(): void
    {
        $c = new CartaPresentacion();
        $c->setId_ubi(1010810);
        $c->setId_direccion(55);
        $this->uc($c, null)->execute(['pres_nom' => 'Nuevo'] + self::entrada());
        $this->assertSame([$c], $this->guardadas['general']);
        $this->assertSame('Nuevo', $c->getPres_nom());
    }

    public function test_errores(): void
    {
        $this->assertFalse($this->uc(null, null)->execute(['id_ubi' => 0, 'id_direccion' => 5])['ok']);
        $this->assertSame(['ok' => false, 'mensaje' => 'Hay un error, no se ha guardado.'], $this->uc(null, $this->centro('dlb'), false)->execute(self::entrada()));
    }
}
