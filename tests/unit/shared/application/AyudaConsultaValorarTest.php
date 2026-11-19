<?php

declare(strict_types=1);

namespace Tests\unit\shared\application;

use PHPUnit\Framework\TestCase;
use src\shared\application\AyudaConsultaValorar;
use src\shared\domain\contracts\AyudaConsultaRepositoryInterface;
use src\shared\domain\entity\AyudaConsulta;

final class AyudaConsultaValorarTest extends TestCase
{
    private const UUID = '550e8400-e29b-41d4-a716-446655440010';

    /** @var list<AyudaConsulta> */
    private array $guardadas = [];

    private function repositorio(?AyudaConsulta $existente, bool $guardaBien = true): AyudaConsultaRepositoryInterface
    {
        $repo = $this->createMock(AyudaConsultaRepositoryInterface::class);
        $repo->method('findById')->willReturnCallback(
            static fn (string $uuid): ?AyudaConsulta => $uuid === self::UUID ? $existente : null
        );
        $repo->method('Guardar')->willReturnCallback(function (AyudaConsulta $c) use ($guardaBien): bool {
            $this->guardadas[] = $c;

            return $guardaBien;
        });

        return $repo;
    }

    private function consulta(): AyudaConsulta
    {
        $c = new AyudaConsulta();
        $c->setUuid_item(self::UUID);
        $c->setPregunta('¿cómo apunto a alguien?');

        return $c;
    }

    public function test_no_me_ha_servido_guarda_la_valoracion_y_el_comentario(): void
    {
        $error = (new AyudaConsultaValorar($this->repositorio($this->consulta())))
            ->execute(self::UUID, false, "  buscaba cómo borrarlo \n");

        $this->assertSame('', $error);
        $this->assertCount(1, $this->guardadas);
        $this->assertFalse($this->guardadas[0]->getUtil());
        $this->assertSame('buscaba cómo borrarlo', $this->guardadas[0]->getComentario());
        $this->assertSame('¿cómo apunto a alguien?', $this->guardadas[0]->getPregunta());
    }

    public function test_me_ha_servido_sin_comentario(): void
    {
        $error = (new AyudaConsultaValorar($this->repositorio($this->consulta())))->execute(self::UUID, true, '');

        $this->assertSame('', $error);
        $this->assertTrue($this->guardadas[0]->getUtil());
        $this->assertNull($this->guardadas[0]->getComentario());
    }

    public function test_un_comentario_muy_largo_se_recorta(): void
    {
        (new AyudaConsultaValorar($this->repositorio($this->consulta())))->execute(self::UUID, false, str_repeat('á', 3000));

        $this->assertSame(1000, mb_strlen((string) $this->guardadas[0]->getComentario()));
    }

    public function test_valorar_de_nuevo_sin_comentario_conserva_el_que_habia(): void
    {
        $consulta = $this->consulta();
        $consulta->setUtil(false);
        $consulta->setComentario('lo de antes');

        (new AyudaConsultaValorar($this->repositorio($consulta)))->execute(self::UUID, false, '');

        $this->assertSame('lo de antes', $this->guardadas[0]->getComentario());
    }

    public function test_la_consulta_que_no_existe_es_un_error(): void
    {
        $error = (new AyudaConsultaValorar($this->repositorio(null)))->execute(self::UUID, false, 'x');

        $this->assertNotSame('', $error);
        $this->assertSame([], $this->guardadas);
    }

    public function test_si_no_se_puede_guardar_es_un_error(): void
    {
        $error = (new AyudaConsultaValorar($this->repositorio($this->consulta(), false)))->execute(self::UUID, true, '');

        $this->assertNotSame('', $error);
    }
}
