<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadestudios\application\FormMatriculasDeUnaPersonaData;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\application\PlanEstudiosDePersona;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\profesores\domain\services\ProfesorStgrService;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;

final class FormMatriculasDeUnaPersonaDataTest extends TestCase
{
    public function test_matricular_ca_solo_lista_asignaturas_del_ca_no_matriculadas(): void
    {
        $ofertaA = new ActividadAsignatura();
        $ofertaA->setId_activ(10);
        $ofertaA->setIdAsignaturaVo(1101);
        $ofertaA->setId_schema(1001);
        $ofertaB = new ActividadAsignatura();
        $ofertaB->setId_activ(10);
        $ofertaB->setIdAsignaturaVo(1202);
        $ofertaB->setId_schema(1001);

        $aaRepo = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $aaRepo->method('getActividadAsignaturas')->with(['id_activ' => 10])->willReturn([$ofertaA, $ofertaB]);

        $ya = $this->createMock(Matricula::class);
        $ya->method('getId_asignatura')->willReturn(1101);
        $matRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $matRepo->method('getMatriculas')->willReturn([$ya]);

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('getArrayAsignaturasCreditos')->willReturn([
            1101 => ['nombre_asignatura' => 'Dogmática', 'creditos' => 3],
            1202 => ['nombre_asignatura' => 'Moral', 'creditos' => 4],
        ]);

        $activ = $this->createMock(ActividadAll::class);
        $activ->method('getNom_activ')->willReturn('CA prueba');
        $activ->method('getDl_org')->willReturn('dlbv');
        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->method('findById')->with(10)->willReturn($activ);

        $out = $this->useCase($actRepo, $asigRepo, $matRepo, $aaRepo)->execute([
            'id_pau' => 7,
            'id_activ' => 10,
            'modo' => FormMatriculasDeUnaPersonaData::MODO_MATRICULAR_CA,
        ]);

        $this->assertTrue($out['alta_desde_ca']);
        $this->assertSame('nuevo', $out['mod']);
        $this->assertSame(FormMatriculasDeUnaPersonaData::MODO_MATRICULAR_CA, $out['a_camposHidden']['modo']);
        $this->assertSame(['1202' => 'Moral'], self::stringifyKeys($out['oDesplAsignaturas_opciones']));
        $this->assertSame('id_asignatura', $out['camposForm']);
        $this->assertSame('false', $out['condicion_js']);
    }

    public function test_matricular_ca_prefija_dl_en_asignatura_de_otra_dl(): void
    {
        $oferta = new ActividadAsignatura();
        $oferta->setId_activ(10);
        $oferta->setIdAsignaturaVo(1202);
        $oferta->setId_schema(2002);

        $aaRepo = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $aaRepo->method('getActividadAsignaturas')->willReturn([$oferta]);

        $matRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $matRepo->method('getMatriculas')->willReturn([]);

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('getArrayAsignaturasCreditos')->willReturn([
            1202 => ['nombre_asignatura' => 'Moral', 'creditos' => 4],
        ]);

        $activ = $this->createMock(ActividadAll::class);
        $activ->method('getNom_activ')->willReturn('CA prueba');
        $activ->method('getDl_org')->willReturn('dlbv');
        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->method('findById')->willReturn($activ);

        $schema = $this->createMock(\src\utils_database\domain\entity\DbSchema::class);
        $schema->method('getId')->willReturn(2002);
        $schema->method('getSchema')->willReturn('H-dlxxv');
        $schemaRepo = $this->createMock(\src\utils_database\domain\contracts\DbSchemaRepositoryInterface::class);
        $schemaRepo->method('getDbSchemas')->willReturn([$schema]);

        $out = $this->useCase($actRepo, $asigRepo, $matRepo, $aaRepo, $schemaRepo)->execute([
            'id_pau' => 7,
            'id_activ' => 10,
            'modo' => FormMatriculasDeUnaPersonaData::MODO_MATRICULAR_CA,
        ]);

        $this->assertSame(['1202' => '(dlxx) Moral'], self::stringifyKeys($out['oDesplAsignaturas_opciones']));
    }

    public function test_matricular_ca_dos_ofertas_misma_asignatura_fuerzan_prefijo(): void
    {
        $propia = new ActividadAsignatura();
        $propia->setId_activ(10);
        $propia->setIdAsignaturaVo(1202);
        $propia->setId_schema(1001);
        $ajena = new ActividadAsignatura();
        $ajena->setId_activ(10);
        $ajena->setIdAsignaturaVo(1202);
        $ajena->setId_schema(2002);

        $aaRepo = $this->createMock(ActividadAsignaturaRepositoryInterface::class);
        $aaRepo->method('getActividadAsignaturas')->willReturn([$propia, $ajena]);

        $matRepo = $this->createMock(MatriculaRepositoryInterface::class);
        $matRepo->method('getMatriculas')->willReturn([]);

        $asigRepo = $this->createMock(AsignaturaRepositoryInterface::class);
        $asigRepo->method('getArrayAsignaturasCreditos')->willReturn([
            1202 => ['nombre_asignatura' => 'Moral', 'creditos' => 4],
        ]);

        $activ = $this->createMock(ActividadAll::class);
        $activ->method('getNom_activ')->willReturn('CA prueba');
        $activ->method('getDl_org')->willReturn('dlbv');
        $actRepo = $this->createMock(ActividadAllRepositoryInterface::class);
        $actRepo->method('findById')->willReturn($activ);

        $schemaPropia = $this->createMock(\src\utils_database\domain\entity\DbSchema::class);
        $schemaPropia->method('getId')->willReturn(1001);
        $schemaPropia->method('getSchema')->willReturn('H-dlbvv');
        $schemaAjena = $this->createMock(\src\utils_database\domain\entity\DbSchema::class);
        $schemaAjena->method('getId')->willReturn(2002);
        $schemaAjena->method('getSchema')->willReturn('H-dlxxv');
        $schemaRepo = $this->createMock(DbSchemaRepositoryInterface::class);
        $schemaRepo->method('getDbSchemas')->willReturn([$schemaPropia, $schemaAjena]);

        $out = $this->useCase($actRepo, $asigRepo, $matRepo, $aaRepo, $schemaRepo)->execute([
            'id_pau' => 7,
            'id_activ' => 10,
            'modo' => FormMatriculasDeUnaPersonaData::MODO_MATRICULAR_CA,
        ]);

        $this->assertSame(
            [
                '1202#1001' => '(dlbv) Moral',
                '1202#2002' => '(dlxx) Moral',
            ],
            self::stringifyKeys($out['oDesplAsignaturas_opciones']),
        );
    }

    /**
     * @param array<int|string, string> $opciones
     * @return array<string, string>
     */
    private static function stringifyKeys(array $opciones): array
    {
        $out = [];
        foreach ($opciones as $k => $v) {
            $out[(string) $k] = $v;
        }

        return $out;
    }

    private function useCase(
        ActividadAllRepositoryInterface $actRepo,
        AsignaturaRepositoryInterface $asigRepo,
        MatriculaRepositoryInterface $matRepo,
        ActividadAsignaturaRepositoryInterface $aaRepo,
        ?DbSchemaRepositoryInterface $schemaRepo = null,
    ): FormMatriculasDeUnaPersonaData {
        $repoPlan = $this->createMock(PersonaNotaRepositoryInterface::class);
        $repoPlan->method('getPersonaNotas')->willReturn([]);

        return new FormMatriculasDeUnaPersonaData(
            $actRepo,
            $asigRepo,
            $matRepo,
            $this->createMock(ProfesorStgrService::class),
            $this->createMock(PersonaNotaRepositoryInterface::class),
            $this->createMock(MatriculaDlRepositoryInterface::class),
            new PlanEstudiosDePersona($repoPlan),
            $aaRepo,
            $schemaRepo ?? $this->createMock(DbSchemaRepositoryInterface::class),
        );
    }
}
