<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\ResiduoOtraRegionComprobarData;
use src\notas\domain\contracts\ResiduoOtraRegionConsultaInterface;

final class ResiduoOtraRegionComprobarDataTest extends TestCase
{
    public function test_separa_paso_acta_pareja_y_json(): void
    {
        $consulta = $this->createMock(ResiduoOtraRegionConsultaInterface::class);
        $consulta->method('listar')->willReturn([
            $this->fila(-10, false, [], false, 'H-Hv'),
            $this->fila(20, true, [], false, 'H-Hv'),
            $this->fila(30, false, ['Galbel 1/26'], true, 'Galbel-crGalbelv'),
        ]);

        $asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $asignaturas->method('getArrayAsignaturas')->willReturn([1101 => 'Latín']);

        $out = (new ResiduoOtraRegionComprobarData($consulta, $asignaturas))->execute();
        $pasos = $out['pasos'];

        $this->assertSame(1, $pasos[0]['numero']);
        $this->assertSame('1', $pasos[0]['tablas'][0]['filas'][1][3]);

        $idsPaso = array_column($pasos[1]['tablas'][0]['filas'], 1);
        $this->assertSame(['-10'], $idsPaso);

        $idsConActa = array_column($pasos[2]['tablas'][0]['filas'], 1);
        $this->assertSame(['20'], $idsConActa);

        $idsSinActa = array_column($pasos[3]['tablas'][2]['filas'], 0);
        $this->assertSame(['30'], $idsSinActa);

        $json = $pasos[4]['tablas'][0]['filas'];
        $this->assertCount(1, $json);
        $this->assertSame('Galbel 1/26', $json[0][4]);
        $this->assertSame(_('sí'), $json[0][5]);
    }

    public function test_inventario_lista_esquemas_sin_permiso(): void
    {
        $consulta = $this->createMock(ResiduoOtraRegionConsultaInterface::class);
        $consulta->method('listar')->willReturn([]);
        $consulta->method('esquemasSinPermiso')->willReturn(['H-Hv']);

        $asignaturas = $this->createMock(AsignaturaRepositoryInterface::class);
        $asignaturas->method('getArrayAsignaturas')->willReturn([]);

        $out = (new ResiduoOtraRegionComprobarData($consulta, $asignaturas))->execute();
        $tablas = $out['pasos'][0]['tablas'];

        $this->assertSame(_('Sin permiso de lectura'), $tablas[1]['titulo']);
        $this->assertSame([['H-Hv']], $tablas[1]['filas']);
    }

    /**
     * @param list<string> $certificados
     * @return array<string, mixed>
     */
    private function fila(
        int $idNom,
        bool $hayActa,
        array $certificados,
        bool $enModulo,
        string $esquema,
    ): array {
        return [
            'esquema' => $esquema,
            'id_nom' => $idNom,
            'id_asignatura' => 1101,
            'id_situacion' => 4,
            'tipo_acta' => 2,
            'acta' => 'Ratio',
            'f_acta' => '1990-01-01',
            'detalle' => '',
            'nombre' => 'Alguien',
            'hay_acta_dl' => $hayActa,
            'certificados' => $certificados,
            'certificado_en_modulo' => $enModulo,
        ];
    }
}
