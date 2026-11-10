<?php

declare(strict_types=1);

namespace Tests\unit\encargossacd\application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\encargossacd\application\EncargoVerNuevo;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\value_objects\EncargoGrupo;

final class EncargoVerNuevoTest extends TestCase
{
    private EncargoRepositoryInterface&MockObject $encargos;
    private EncargoTipoRepositoryInterface&MockObject $tipos;
    private EncargoVerNuevo $useCase;

    protected function setUp(): void
    {
        $this->encargos = $this->createMock(EncargoRepositoryInterface::class);
        $this->tipos = $this->createMock(EncargoTipoRepositoryInterface::class);
        $this->useCase = new EncargoVerNuevo($this->encargos, $this->tipos);
    }

    /** @return array<string, string> */
    private function input(array $extra = []): array
    {
        return array_merge([
            'filtro_ctr' => '', 'lst_ctrs' => '42', 'id_zona' => '3', 'desc_enc' => 'Misa diaria',
            'idioma_enc' => 'es_ES.UTF-8', 'desc_lugar' => 'Capilla', 'observ' => 'nota', 'id_tipo_enc' => '2100',
        ], $extra);
    }

    private function capturar(?Encargo &$guardado): void
    {
        $this->encargos->method('getNewId')->willReturn(500);
        $this->encargos->method('Guardar')->willReturnCallback(function (Encargo $e) use (&$guardado): bool {
            $guardado = $e;

            return true;
        });
    }

    public function test_guarda_el_encargo_con_todos_los_campos(): void
    {
        $this->capturar($g);

        $this->assertSame(['error' => ''], $this->useCase->execute($this->input()));

        $this->assertSame(500, $g->getId_enc());
        $this->assertSame(2100, $g->getId_tipo_enc());
        $this->assertSame(EncargoGrupo::CENTRO_SV, $g->getGrupo_encargo(), 'sin filtro_ctr: centros sv');
        $this->assertSame(42, $g->getId_ubi());
        $this->assertSame(3, $g->getId_zona());
        $this->assertSame('Misa diaria', $g->getDesc_enc());
        $this->assertSame('es_ES.UTF-8', $g->getIdioma_enc());
        $this->assertSame('Capilla', $g->getDesc_lugar());
        $this->assertSame('nota', $g->getObserv());
    }

    public function test_el_grupo_sale_del_filtro_de_centros(): void
    {
        $this->capturar($g);

        $this->useCase->execute($this->input(['filtro_ctr' => (string) EncargoGrupo::CENTRO_SF]));

        $this->assertSame(EncargoGrupo::CENTRO_SF, $g->getGrupo_encargo());
    }

    public function test_un_tipo_que_empieza_por_7_es_un_encargo_personal(): void
    {
        $this->capturar($g);

        $this->useCase->execute($this->input(['id_tipo_enc' => '7100', 'filtro_ctr' => (string) EncargoGrupo::CENTRO_SF]));

        $this->assertSame(EncargoGrupo::PERSONAL, $g->getGrupo_encargo(), 'el tipo 7xxx manda sobre el filtro');
    }

    public function test_tipo_completo_no_consulta_el_repositorio_de_tipos(): void
    {
        $this->tipos->expects($this->never())->method('id_tipo_encargo');
        $this->capturar($g);

        $this->useCase->execute($this->input(['id_tipo_enc' => '2100']));
    }

    /**
     * Un tipo vacío o con comodines («21..») se resuelve con el grupo y el nombre del tipo.
     *
     * @dataProvider tiposIncompletosProvider
     */
    public function test_tipo_incompleto_se_resuelve_con_grupo_y_nombre(string $idTipo): void
    {
        $this->tipos->expects($this->once())->method('id_tipo_encargo')->with('2', 'misa')->willReturn('2100');
        $this->capturar($g);

        $out = $this->useCase->execute($this->input(['id_tipo_enc' => $idTipo, 'grupo' => '2', 'nom_tipo' => 'misa']));

        $this->assertSame(['error' => ''], $out);
        $this->assertSame(2100, $g->getId_tipo_enc());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function tiposIncompletosProvider(): iterable
    {
        yield 'vacío' => [''];
        yield 'con comodines' => ['21..'];
    }

    public function test_si_el_tipo_sigue_incompleto_pide_seleccionarlo_y_no_guarda(): void
    {
        $this->tipos->method('id_tipo_encargo')->willReturn('21..');
        $this->encargos->expects($this->never())->method('Guardar');

        $out = $this->useCase->execute($this->input(['id_tipo_enc' => '']));

        $this->assertNotSame('', $out['error']);
    }

    public function test_sin_descripcion_no_guarda(): void
    {
        $this->encargos->expects($this->never())->method('Guardar');

        $out = $this->useCase->execute($this->input(['desc_enc' => '']));

        $this->assertNotSame('', $out['error']);
    }

    public function test_si_falla_el_guardado_devuelve_el_error_del_repositorio(): void
    {
        $this->encargos->method('getNewId')->willReturn(500);
        $this->encargos->method('Guardar')->willReturn(false);
        $this->encargos->method('getErrorTxt')->willReturn('clave duplicada');

        $out = $this->useCase->execute($this->input());

        $this->assertStringContainsString('clave duplicada', $out['error']);
    }

    public function test_un_grupo_fuera_de_los_validos_se_rechaza_con_excepcion(): void
    {
        // Comportamiento actual: EncargoGrupo valida y lanza; la rama «grupo no válido» del caso de uso es inalcanzable.
        $this->encargos->method('getNewId')->willReturn(500);
        $this->encargos->expects($this->never())->method('Guardar');

        $this->expectException(\InvalidArgumentException::class);
        $this->useCase->execute($this->input(['filtro_ctr' => '99']));
    }

    public function test_sin_idioma_se_guarda_igualmente(): void
    {
        $this->capturar($g);

        $this->assertSame(['error' => ''], $this->useCase->execute($this->input(['idioma_enc' => ''])));
    }

    /**
     * Comportamiento actual: un idioma con formato incorrecto lo rechaza el value object con una
     * excepción que el caso de uso no captura (no devuelve ['error' => …]).
     */
    public function test_un_idioma_con_formato_incorrecto_lanza_excepcion(): void
    {
        $this->encargos->method('getNewId')->willReturn(500);

        $this->expectException(\InvalidArgumentException::class);
        $this->useCase->execute($this->input(['idioma_enc' => 'es']));
    }
}
