<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application\support;

use PHPUnit\Framework\TestCase;
use src\actividadestudios\application\support\AsignaturaNombreDlPrefix;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use src\utils_database\domain\entity\DbSchema;

final class AsignaturaNombreDlPrefixTest extends TestCase
{
    public function test_otra_dl_lleva_prefijo_y_la_organizadora_no(): void
    {
        $this->assertSame(
            'Latín I',
            AsignaturaNombreDlPrefix::aplicar('Latín I', 'dlbv', 'dlbv'),
        );
        $this->assertSame(
            '(dlxx) Latín I',
            AsignaturaNombreDlPrefix::aplicar('Latín I', 'dlxx', 'dlbv'),
        );
    }

    public function test_normaliza_sufijo_sf_de_dl_org(): void
    {
        $this->assertSame(
            'Latín I',
            AsignaturaNombreDlPrefix::aplicar('Latín I', 'dlbv', 'dlbvf'),
        );
        $this->assertSame(
            'dlbv',
            AsignaturaNombreDlPrefix::dlDesdeEsquema('H-dlbvf'),
        );
    }

    public function test_duplicado_fuerza_prefijo_tambien_en_la_propia(): void
    {
        $this->assertSame(
            '(dlbv) Latín I',
            AsignaturaNombreDlPrefix::aplicar('Latín I', 'dlbv', 'dlbv', true),
        );
    }

    public function test_dl_desde_id_schema_elige_la_fila_con_ese_id(): void
    {
        $propia = $this->createMock(DbSchema::class);
        $propia->method('getId')->willReturn(1001);
        $propia->method('getSchema')->willReturn('H-dlbvv');
        $ajena = $this->createMock(DbSchema::class);
        $ajena->method('getId')->willReturn(2002);
        $ajena->method('getSchema')->willReturn('H-dlxxv');

        $repo = $this->createMock(DbSchemaRepositoryInterface::class);
        $repo->method('getDbSchemas')->willReturn([$propia, $ajena]);

        $this->assertSame('dlxx', AsignaturaNombreDlPrefix::dlDesdeIdSchema($repo, 2002));
        $this->assertSame('dlbv', AsignaturaNombreDlPrefix::dlDesdeIdSchema($repo, 1001));
    }

    public function test_dl_para_fila_matricula_usa_esquema_de_la_fila_si_hay_duplicado(): void
    {
        $ofertas = [
            1101 => [
                ['id_schema' => 1001, 'dl' => 'dlbv'],
                ['id_schema' => 2002, 'dl' => 'dlxx'],
            ],
        ];
        $this->assertSame(
            'dlxx',
            AsignaturaNombreDlPrefix::dlParaFilaMatricula(1101, 2002, 1001, $ofertas),
        );
        $this->assertSame(
            'dlbv',
            AsignaturaNombreDlPrefix::dlParaFilaMatricula(1101, 0, 1001, $ofertas),
        );
        $this->assertSame(
            '(dlxx) Latín I',
            AsignaturaNombreDlPrefix::aplicar('Latín I', 'dlxx', 'dlbv', true),
        );
    }

    public function test_id_asignatura_de_opcion_compuesta(): void
    {
        $this->assertSame(1202, AsignaturaNombreDlPrefix::idAsignaturaDeOpcion('1202'));
        $this->assertSame(1202, AsignaturaNombreDlPrefix::idAsignaturaDeOpcion('1202#2002'));
        $this->assertSame(0, AsignaturaNombreDlPrefix::idAsignaturaDeOpcion(''));
    }
}
