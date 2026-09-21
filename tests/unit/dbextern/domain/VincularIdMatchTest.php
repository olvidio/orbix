<?php

declare(strict_types=1);

namespace Tests\unit\dbextern\domain;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\entity\IdMatchPersona;
use src\dbextern\domain\entity\PersonaBDU;
use src\dbextern\domain\VincularIdMatch;

final class VincularIdMatchTest extends TestCase
{
    /** @var IdMatchPersonaRepositoryInterface&MockObject */
    private IdMatchPersonaRepositoryInterface $idMatchRepository;

    /** @var PersonaBDURepositoryInterface&MockObject */
    private PersonaBDURepositoryInterface $personaBDURepository;

    private VincularIdMatch $vincular;

    protected function setUp(): void
    {
        parent::setUp();
        $this->idMatchRepository = $this->createMock(IdMatchPersonaRepositoryInterface::class);
        $this->personaBDURepository = $this->createMock(PersonaBDURepositoryInterface::class);
        $this->vincular = new VincularIdMatch($this->idMatchRepository, $this->personaBDURepository);
    }

    public function test_inserta_si_no_hay_match_previo(): void
    {
        $this->idMatchRepository->method('getIdMatchPersonas')->willReturn([]);
        $this->idMatchRepository->expects($this->once())
            ->method('Guardar')
            ->with($this->callback(static function (IdMatchPersona $match): bool {
                return $match->getId_listas() === 2001
                    && $match->getId_orbix() === 100133837
                    && $match->getId_tabla() === 'n';
            }))
            ->willReturn(true);

        $error = $this->vincular->vincular(2001, 100133837, 'n');

        $this->assertSame('', $error);
    }

    public function test_ya_unido_devuelve_exito_sin_guardar(): void
    {
        $existente = $this->match(2001, 100133837, 'n');
        $this->idMatchRepository->method('getIdMatchPersonas')->willReturnCallback(
            static function (array $where) use ($existente): array {
                if (($where['id_listas'] ?? null) === 2001 || ($where['id_orbix'] ?? null) === 100133837) {
                    return [$existente];
                }

                return [];
            }
        );
        $this->idMatchRepository->expects($this->never())->method('Guardar');

        $this->assertSame('', $this->vincular->vincular(2001, 100133837, 'n'));
    }

    public function test_reasigna_si_id_listas_ya_no_existe_en_bdu(): void
    {
        $huerfano = $this->match(999, 100133837, 'n');
        $this->idMatchRepository->method('getIdMatchPersonas')->willReturnCallback(
            static function (array $where) use ($huerfano): array {
                if (($where['id_orbix'] ?? null) === 100133837) {
                    return [$huerfano];
                }

                return [];
            }
        );
        $this->personaBDURepository->method('findById')->with(999)->willReturn(null);
        $this->idMatchRepository->expects($this->once())->method('Eliminar')->with($huerfano)->willReturn(true);
        $this->idMatchRepository->expects($this->once())
            ->method('Guardar')
            ->with($this->callback(static function (IdMatchPersona $match): bool {
                return $match->getId_listas() === 2001 && $match->getId_orbix() === 100133837;
            }))
            ->willReturn(true);

        $this->assertSame('', $this->vincular->vincular(2001, 100133837, 'n'));
    }

    public function test_no_roba_si_id_orbix_ya_esta_unido_a_persona_bdu_viva(): void
    {
        $ocupado = $this->match(1500, 100133837, 'n');
        $this->idMatchRepository->method('getIdMatchPersonas')->willReturnCallback(
            static function (array $where) use ($ocupado): array {
                if (($where['id_orbix'] ?? null) === 100133837) {
                    return [$ocupado];
                }

                return [];
            }
        );
        $viva = new PersonaBDU();
        $viva->setIdentif(1500);
        $viva->setApenom('Pérez, Juan');
        $this->personaBDURepository->method('findById')->with(1500)->willReturn($viva);
        $this->idMatchRepository->expects($this->never())->method('Eliminar');
        $this->idMatchRepository->expects($this->never())->method('Guardar');

        $error = $this->vincular->vincular(2001, 100133837, 'n');

        $this->assertNotSame('', $error);
        $this->assertStringContainsString('1500', $error);
    }

    public function test_no_lanza_si_guardar_revienta_por_unicidad(): void
    {
        $this->idMatchRepository->method('getIdMatchPersonas')->willReturn([]);
        $this->idMatchRepository->method('Guardar')->willThrowException(
            new RuntimeException('llave duplicada viola restricción de unicidad «conv_id_personas_id_orbix_key»')
        );

        $error = $this->vincular->vincular(2001, 100133837, 'n');

        $this->assertStringContainsString('conv_id_personas_id_orbix_key', $error);
    }

    public function test_es_match_vigente_false_si_id_listas_desaparecido(): void
    {
        $this->personaBDURepository->method('findById')->with(999)->willReturn(null);

        $this->assertFalse($this->vincular->esMatchVigente($this->match(999, 100133837, 'n')));
    }

    public function test_es_match_vigente_false_si_apenom_vacio(): void
    {
        $persona = new PersonaBDU();
        $persona->setIdentif(999);
        $persona->setApenom('');
        $this->personaBDURepository->method('findById')->with(999)->willReturn($persona);

        $this->assertFalse($this->vincular->esMatchVigente($this->match(999, 100133837, 'n')));
    }

    private function match(int $idListas, int $idOrbix, string $tabla): IdMatchPersona
    {
        $match = new IdMatchPersona();
        $match->setId_listas($idListas);
        $match->setId_orbix($idOrbix);
        $match->setId_tabla($tabla);

        return $match;
    }
}
