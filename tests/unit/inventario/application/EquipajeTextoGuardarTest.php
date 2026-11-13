<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\EquipajeTextoGuardar;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\entity\Egm;
use src\inventario\domain\entity\Equipaje;
use Tests\support\DescribeCriteria;

/**
 * Guardar los textos del listado de un equipaje ({@see EquipajeTextoGuardar}): cabeceras, pie y texto
 * de una maleta; texto demasiado largo (con el límite de cada campo) y grupo o equipaje inexistentes.
 */
final class EquipajeTextoGuardarTest extends TestCase
{
    private ?Equipaje $equipaje = null;
    private ?Egm $egm = null;
    /** @var list<string> */
    private array $guardados = [];

    protected function setUp(): void
    {
        $this->equipaje = new Equipaje();
        $this->equipaje->setId_equipaje(5);
        $this->egm = new Egm();
        $this->egm->setId_item(900);
    }

    private function uc(): EquipajeTextoGuardar
    {
        $equipajes = $this->createMock(EquipajeRepositoryInterface::class);
        $equipajes->method('findById')->willReturnCallback(fn (int $id): ?Equipaje => $id === 5 ? $this->equipaje : null);
        $equipajes->method('Guardar')->willReturnCallback(function (): bool {
            $this->guardados[] = 'equipaje';
            return true;
        });
        $egms = $this->createMock(EgmRepositoryInterface::class);
        $egms->method('matching')->willReturnCallback(function ($c): array {
            return DescribeCriteria::filtros($c) === ['id_equipaje eq 5', 'id_grupo eq 2'] ? [$this->egm] : [];
        });
        $egms->method('Guardar')->willReturnCallback(function (): bool {
            $this->guardados[] = 'egm';
            return true;
        });

        return new EquipajeTextoGuardar($equipajes, $egms);
    }

    public function test_cabeceras_pie_y_texto_de_una_maleta(): void
    {
        $uc = $this->uc();
        $this->assertSame('', $uc->execute(5, 'cabecera', 'Cabecera A'));
        $this->assertSame('', $uc->execute(5, 'cabeceraB', str_repeat('b', 400)));
        $this->assertSame('', $uc->execute(5, 'pie', str_repeat('p', 2000)));
        $this->assertSame('', $uc->execute(5, 'docs_grupo_2', 'Para el cl'));

        $this->assertSame(['Cabecera A', 400, 2000], [$this->equipaje->getCabecera(), mb_strlen((string) $this->equipaje->getCabecerab()), mb_strlen((string) $this->equipaje->getPie())]);
        $this->assertSame('Para el cl', $this->egm->getTextoVo()?->value());
        $this->assertSame(['equipaje', 'equipaje', 'equipaje', 'egm'], $this->guardados);
    }

    public function test_texto_demasiado_largo_dice_el_limite_de_ese_campo(): void
    {
        $uc = $this->uc();
        $this->assertSame('El texto no puede superar 400 caracteres', $uc->execute(5, 'cabecera', str_repeat('x', 401)));
        $this->assertSame('El texto no puede superar 400 caracteres', $uc->execute(5, 'cabeceraB', str_repeat('x', 401)));
        $this->assertSame('El texto no puede superar 2000 caracteres', $uc->execute(5, 'pie', str_repeat('x', 2001)));
        $this->assertSame('El texto no puede superar 255 caracteres', $uc->execute(5, 'docs_grupo_2', str_repeat('x', 256)));
        $this->assertSame([], $this->guardados);
    }

    public function test_grupo_o_equipaje_inexistentes_y_lugar_desconocido(): void
    {
        $uc = $this->uc();
        $this->assertSame('hay un error, no se ha guardado', $uc->execute(5, 'docs_grupo_9', 'x'), 'antes: método sobre null');
        $this->assertSame('', $uc->execute(6, 'pie', 'x'));
        $this->assertSame('', $uc->execute(5, 'otra_cosa', 'x'));
        $this->assertSame([], $this->guardados);
    }
}
