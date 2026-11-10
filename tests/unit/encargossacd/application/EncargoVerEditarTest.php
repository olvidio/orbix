<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\EncargoVerEditar;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;

final class EncargoVerEditarTest extends TestCase
{
    private EncargoRepositoryInterface&MockObject $encargos;
    private EncargoVerEditar $useCase;

    protected function setUp(): void
    {
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->useCase = new EncargoVerEditar($this->encargos);
    }

    /** @return array<string, string> */
    private function input(array $extra = []): array
    {
        return array_merge([
            'filtro_ctr' => '2', 'id_enc' => '77', 'lst_ctrs' => '42', 'id_zona' => '3', 'desc_enc' => 'Misa diaria',
            'idioma_enc' => 'es_ES.UTF-8', 'desc_lugar' => 'Capilla', 'observ' => 'nota', 'id_tipo_enc' => '2100',
        ], $extra);
    }

    public function test_actualiza_el_encargo_existente_y_lo_guarda(): void
    {
        $e = new Encargo();
        $e->setId_enc(77);
        $this->encargos->expects($this->once())->method('findById')->with(77)->willReturn($e);
        $this->encargos->expects($this->once())->method('Guardar')->with($e)->willReturn(true);

        $this->assertSame(['error' => ''], $this->useCase->execute($this->input()));

        $this->assertSame(77, $e->getId_enc(), 'no cambia el id');
        $this->assertSame(2100, $e->getId_tipo_enc());
        $this->assertSame(2, $e->getGrupo_encargo());
        $this->assertSame(42, $e->getId_ubi());
        $this->assertSame(3, $e->getId_zona());
        $this->assertSame('Misa diaria', $e->getDesc_enc());
        $this->assertSame('es_ES.UTF-8', $e->getIdioma_enc());
        $this->assertSame('Capilla', $e->getDesc_lugar());
        $this->assertSame('nota', $e->getObserv());
    }

    public function test_sin_filtro_de_centros_el_grupo_es_1(): void
    {
        $e = new Encargo();
        $this->encargos->method('findById')->willReturn($e);
        $this->encargos->method('Guardar')->willReturn(true);

        $this->useCase->execute($this->input(['filtro_ctr' => '']));

        $this->assertSame(1, $e->getGrupo_encargo());
    }

    public function test_sin_descripcion_no_busca_ni_guarda(): void
    {
        $this->encargos->expects($this->never())->method('findById');
        $this->encargos->expects($this->never())->method('Guardar');

        $this->assertNotSame('', $this->useCase->execute($this->input(['desc_enc' => '']))['error']);
    }

    public function test_un_encargo_inexistente_indica_su_numero(): void
    {
        $this->encargos->method('findById')->willReturn(null);
        $this->encargos->expects($this->never())->method('Guardar');

        $out = $this->useCase->execute($this->input(['id_enc' => '77']));

        $this->assertStringContainsString('77', $out['error']);
    }

    public function test_si_falla_el_guardado_devuelve_el_error_del_repositorio(): void
    {
        $this->encargos->method('findById')->willReturn(new Encargo());
        $this->encargos->method('Guardar')->willReturn(false);
        $this->encargos->method('getErrorTxt')->willReturn('boom');

        $this->assertStringContainsString('boom', $this->useCase->execute($this->input())['error']);
    }

    public function test_sin_idioma_se_guarda_igualmente(): void
    {
        $e = new Encargo();
        $this->encargos->method('findById')->willReturn($e);
        $this->encargos->method('Guardar')->willReturn(true);

        $this->assertSame(['error' => ''], $this->useCase->execute($this->input(['idioma_enc' => ''])));
    }

    /**
     * Comportamiento actual: un idioma con formato incorrecto lo rechaza el value object con una
     * excepción que el caso de uso no captura (no devuelve ['error' => …]).
     */
    public function test_un_idioma_con_formato_incorrecto_lanza_excepcion(): void
    {
        $this->encargos->method('findById')->willReturn(new Encargo());

        $this->expectException(\InvalidArgumentException::class);
        $this->useCase->execute($this->input(['idioma_enc' => 'es']));
    }
}
