<?php

declare(strict_types=1);

namespace Tests\unit\shared\application;

use PHPUnit\Framework\TestCase;
use src\shared\application\AyudaConsultasCambiarEstado;
use src\shared\domain\contracts\AyudaConsultaRepositoryInterface;
use src\shared\domain\entity\AyudaConsulta;
use src\shared\domain\value_objects\AyudaConsultaEstado;

final class AyudaConsultasCambiarEstadoTest extends TestCase
{
    private const UNA = '550e8400-e29b-41d4-a716-446655440021';
    private const OTRA = '550e8400-e29b-41d4-a716-446655440022';
    private const NO_EXISTE = '550e8400-e29b-41d4-a716-446655440029';

    /** @var array<string, int> uuid => estado guardado */
    private array $guardadas = [];

    private function repositorio(bool $guardaBien = true): AyudaConsultaRepositoryInterface
    {
        $repo = $this->createMock(AyudaConsultaRepositoryInterface::class);
        $repo->method('findById')->willReturnCallback(static function (string $uuid): ?AyudaConsulta {
            if (!in_array($uuid, [self::UNA, self::OTRA], true)) {
                return null;
            }
            $c = new AyudaConsulta();
            $c->setUuid_item($uuid);
            $c->setPregunta('p');

            return $c;
        });
        $repo->method('Guardar')->willReturnCallback(function (AyudaConsulta $c) use ($guardaBien): bool {
            $this->guardadas[$c->getUuid_item()->value()] = $c->getEstado();

            return $guardaBien;
        });

        return $repo;
    }

    public function test_cambia_el_estado_de_todas_las_consultas_del_grupo(): void
    {
        $error = (new AyudaConsultasCambiarEstado($this->repositorio()))
            ->execute([self::UNA, self::OTRA], AyudaConsultaEstado::RESUELTA);

        $this->assertSame('', $error);
        $this->assertSame(
            [self::UNA => AyudaConsultaEstado::RESUELTA, self::OTRA => AyudaConsultaEstado::RESUELTA],
            $this->guardadas
        );
    }

    public function test_las_que_ya_se_purgaron_se_saltan(): void
    {
        $error = (new AyudaConsultasCambiarEstado($this->repositorio()))
            ->execute([self::NO_EXISTE, self::UNA], AyudaConsultaEstado::DESCARTADA);

        $this->assertSame('', $error);
        $this->assertSame([self::UNA => AyudaConsultaEstado::DESCARTADA], $this->guardadas);
    }

    public function test_si_no_queda_ninguna_es_un_error(): void
    {
        $error = (new AyudaConsultasCambiarEstado($this->repositorio()))
            ->execute([self::NO_EXISTE], AyudaConsultaEstado::RESUELTA);

        $this->assertNotSame('', $error);
        $this->assertSame([], $this->guardadas);
    }

    public function test_sin_consultas_es_un_error(): void
    {
        $this->assertNotSame('', (new AyudaConsultasCambiarEstado($this->repositorio()))->execute([], 2));
    }

    public function test_un_estado_que_no_existe_es_un_error_y_no_guarda(): void
    {
        $error = (new AyudaConsultasCambiarEstado($this->repositorio()))->execute([self::UNA], 9);

        $this->assertNotSame('', $error);
        $this->assertSame([], $this->guardadas);
    }

    public function test_si_no_se_puede_guardar_es_un_error(): void
    {
        $error = (new AyudaConsultasCambiarEstado($this->repositorio(false)))
            ->execute([self::UNA], AyudaConsultaEstado::RESUELTA);

        $this->assertNotSame('', $error);
    }
}
