<?php

declare(strict_types=1);

namespace Tests\unit\personas\application\services;

use PHPUnit\Framework\TestCase;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\contracts\PersonaAllRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryFactoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaPub;
use src\personas\domain\value_objects\SituacionCode;

final class PersonaFinderServiceLookupTest extends TestCase
{
    public function test_find_persona_en_global_usa_local_si_existe(): void
    {
        $local = $this->createMock(PersonaDl::class);

        $dlRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $dlRepo->method('getPersonas')->willReturn([$local]);

        $factory = $this->createMock(PersonaDlRepositoryFactoryInterface::class);
        $factory->method('create')->willReturn($dlRepo);

        $personaAll = $this->createMock(PersonaAllRepositoryInterface::class);
        $personaAll->expects($this->never())->method('findByIdNomParaLookup');

        $service = $this->buildService($factory, $personaAll);

        $this->assertSame($local, $service->findPersonaEnGlobal(10));
    }

    public function test_find_persona_en_global_fallback_a_global_personas(): void
    {
        $dlRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $dlRepo->method('getPersonas')->willReturn([]);

        $factory = $this->createMock(PersonaDlRepositoryFactoryInterface::class);
        $factory->method('create')->willReturn($dlRepo);

        $pub = $this->createMock(PersonaPub::class);
        $pub->method('getSituacion')->willReturn('A');
        $pub->method('getSituacionVo')->willReturn(new SituacionCode('A'));
        $pub->method('getId_schema')->willReturn(7);

        $personaAll = $this->createMock(PersonaAllRepositoryInterface::class);
        $personaAll->expects($this->once())
            ->method('findByIdNomParaLookup')
            ->with(10, null)
            ->willReturn($pub);

        $personaPub = $this->createMock(PersonaPubRepositoryInterface::class);
        $personaPub->expects($this->never())->method('findByIdParaListado');

        $service = $this->buildService($factory, $personaAll, $personaPub);

        $found = $service->findPersonaEnGlobal(10);
        $this->assertSame($pub, $found);
        $this->assertSame(7, $found->getId_schema());
    }

    public function test_find_persona_en_global_descarta_situacion_no_activa(): void
    {
        $dlRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $dlRepo->method('getPersonas')->willReturn([]);

        $factory = $this->createMock(PersonaDlRepositoryFactoryInterface::class);
        $factory->method('create')->willReturn($dlRepo);

        $pub = $this->createMock(PersonaPub::class);
        $pub->method('getSituacion')->willReturn('B');

        $personaAll = $this->createMock(PersonaAllRepositoryInterface::class);
        $personaAll->method('findByIdNomParaLookup')->willReturn($pub);

        $service = $this->buildService($factory, $personaAll);

        $this->assertNull($service->findPersonaEnGlobal(10));
    }

    public function test_incluyendo_no_activos_devuelve_local_sin_situacion_a(): void
    {
        $local = $this->createMock(PersonaDl::class);

        $dlRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $dlRepo->method('getPersonas')->willReturnCallback(
            static function (array $aWhere) use ($local): array {
                if (($aWhere['situacion'] ?? null) === 'A') {
                    return [];
                }

                return [$local];
            }
        );

        $factory = $this->createMock(PersonaDlRepositoryFactoryInterface::class);
        $factory->method('create')->willReturn($dlRepo);

        $personaAll = $this->createMock(PersonaAllRepositoryInterface::class);
        $personaAll->method('findByIdNomParaLookup')->willReturn(null);

        $service = $this->buildService($factory, $personaAll);

        $this->assertSame($local, $service->findPersonaEnGlobalIncluyendoNoActivos(10));
    }

    public function test_incluyendo_no_activos_acepta_global_no_activo(): void
    {
        $dlRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $dlRepo->method('getPersonas')->willReturn([]);

        $factory = $this->createMock(PersonaDlRepositoryFactoryInterface::class);
        $factory->method('create')->willReturn($dlRepo);

        $pub = $this->createMock(PersonaPub::class);
        $pub->method('getSituacion')->willReturn('B');

        $personaAll = $this->createMock(PersonaAllRepositoryInterface::class);
        $personaAll->method('findByIdNomParaLookup')->willReturn($pub);

        $service = $this->buildService($factory, $personaAll);

        $this->assertNull($service->findPersonaEnGlobal(10));
        $this->assertSame($pub, $service->findPersonaEnGlobalIncluyendoNoActivos(10));
    }

    public function test_find_persona_en_global_pasa_id_schema_si_se_indica(): void
    {
        $dlRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $dlRepo->method('getPersonas')->willReturn([]);

        $factory = $this->createMock(PersonaDlRepositoryFactoryInterface::class);
        $factory->method('create')->willReturn($dlRepo);

        $pub = $this->createMock(PersonaPub::class);
        $pub->method('getSituacion')->willReturn('A');

        $personaAll = $this->createMock(PersonaAllRepositoryInterface::class);
        $personaAll->expects($this->once())
            ->method('findByIdNomParaLookup')
            ->with(10, 5)
            ->willReturn($pub);

        $service = $this->buildService($factory, $personaAll);

        $problemas = [];
        $this->assertSame($pub, $service->findPersonaEnGlobal(10, $problemas, 5));
    }

    public function test_de_paso_busca_en_p_de_paso_ex_sin_pasar_por_global(): void
    {
        $idDePaso = -100162859;
        $personaEx = $this->createMock(PersonaEx::class);

        $personaExRepo = $this->createMock(PersonaExRepositoryInterface::class);
        $personaExRepo->expects($this->once())
            ->method('findById')
            ->with($idDePaso)
            ->willReturn($personaEx);

        $dlRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $dlRepo->expects($this->never())->method('getPersonas');

        $factory = $this->createMock(PersonaDlRepositoryFactoryInterface::class);
        $factory->expects($this->never())->method('create');

        $personaAll = $this->createMock(PersonaAllRepositoryInterface::class);
        $personaAll->expects($this->never())->method('findByIdNomParaLookup');

        $service = $this->buildService($factory, $personaAll, null, $personaExRepo);

        $this->assertSame($personaEx, $service->findPersonaEnGlobalODePaso($idDePaso));
    }

    public function test_de_paso_id_positivo_no_consulta_ex(): void
    {
        $local = $this->createMock(PersonaDl::class);

        $dlRepo = $this->createMock(PersonaDlRepositoryInterface::class);
        $dlRepo->method('getPersonas')->willReturn([$local]);

        $factory = $this->createMock(PersonaDlRepositoryFactoryInterface::class);
        $factory->method('create')->willReturn($dlRepo);

        $personaExRepo = $this->createMock(PersonaExRepositoryInterface::class);
        $personaExRepo->expects($this->never())->method('findById');

        $service = $this->buildService(
            $factory,
            $this->createMock(PersonaAllRepositoryInterface::class),
            null,
            $personaExRepo,
        );

        $this->assertSame($local, $service->findPersonaEnGlobalODePaso(10));
    }

    public function test_para_listado_de_paso_usa_p_de_paso_ex(): void
    {
        $idDePaso = -100162859;
        $personaEx = $this->createMock(PersonaEx::class);

        $personaExRepo = $this->createMock(PersonaExRepositoryInterface::class);
        $personaExRepo->expects($this->once())
            ->method('findById')
            ->with($idDePaso)
            ->willReturn($personaEx);

        $factory = $this->createMock(PersonaDlRepositoryFactoryInterface::class);
        $factory->expects($this->never())->method('create');

        $personaAll = $this->createMock(PersonaAllRepositoryInterface::class);
        $personaAll->expects($this->never())->method('findByIdNomParaLookup');

        $service = $this->buildService($factory, $personaAll, null, $personaExRepo);

        $problemas = [];
        $marca = false;
        $this->assertSame($personaEx, $service->findPersonaParaListado($idDePaso, $problemas, $marca));
    }

    public function test_incluyendo_no_activos_de_paso_usa_p_de_paso_ex(): void
    {
        $idDePaso = -100162859;
        $personaEx = $this->createMock(PersonaEx::class);

        $personaExRepo = $this->createMock(PersonaExRepositoryInterface::class);
        $personaExRepo->expects($this->once())
            ->method('findById')
            ->with($idDePaso)
            ->willReturn($personaEx);

        $service = $this->buildService(
            $this->createMock(PersonaDlRepositoryFactoryInterface::class),
            $this->createMock(PersonaAllRepositoryInterface::class),
            null,
            $personaExRepo,
        );

        $this->assertSame($personaEx, $service->findPersonaEnGlobalIncluyendoNoActivos($idDePaso));
    }

    private function buildService(
        PersonaDlRepositoryFactoryInterface $factory,
        PersonaAllRepositoryInterface $personaAll,
        ?PersonaPubRepositoryInterface $personaPub = null,
        ?PersonaExRepositoryInterface $personaEx = null,
    ): PersonaFinderService {
        return new PersonaFinderService(
            $factory,
            $personaPub ?? $this->createMock(PersonaPubRepositoryInterface::class),
            $personaEx ?? $this->createMock(PersonaExRepositoryInterface::class),
            $personaAll,
        );
    }
}
