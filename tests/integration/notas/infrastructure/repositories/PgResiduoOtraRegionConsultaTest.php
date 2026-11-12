<?php

declare(strict_types=1);

namespace Tests\integration\notas\infrastructure\repositories;

use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\notas\domain\entity\PersonaNotaOtraRegionStgr;
use src\notas\infrastructure\persistence\postgresql\PgResiduoOtraRegionConsulta;
use Tests\factories\notas\PersonaNotaOtraRegionStgrFactory;
use Tests\myTest;

/**
 * `PgResiduoOtraRegionConsulta::listar()` contra la BD: cruza el residuo de `e_notas_otra_region_stgr` de cada
 * región con las notas de las dl (`hay_acta_dl`), el nombre de la persona y los certificados emitidos.
 *
 * Se añaden dos filas al residuo de `H-Hv` y se borran al acabar: una de una persona que ya tiene esa asignatura en
 * una dl (se busca en la BD, no se fija el id) y otra de un id inexistente.
 */
final class PgResiduoOtraRegionConsultaTest extends myTest
{
    private const ESQUEMA = 'H-Hv';
    private const ID_NOM_INEXISTENTE = 99000071;

    private PersonaNotaOtraRegionStgrRepositoryInterface $repository;
    /** @var list<PersonaNotaOtraRegionStgr> */
    private array $creadas = [];

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => self::ESQUEMA]);
    }

    public function tearDown(): void
    {
        foreach ($this->creadas as $nota) {
            $this->repository->Eliminar($nota);
        }
        parent::tearDown();
    }

    /**
     * @param list<array{certificado: string}>|null $certificados
     */
    private function residuo(int $idNom, int $idAsignatura, ?array $certificados): void
    {
        $nota = (new PersonaNotaOtraRegionStgrFactory())->createSimple();
        $nota->setId_nom($idNom);
        $nota->setId_nivel($idAsignatura);
        $nota->setIdAsignaturaVo(new \src\asignaturas\domain\value_objects\AsignaturaId($idAsignatura));
        $nota->setJson_certificados($certificados);
        $this->assertTrue($this->repository->Guardar($nota), $this->repository->getErrorTxt());
        $this->creadas[] = $nota;
    }

    /**
     * Una persona con una nota en alguna dl y en `global.personas`, sin residuo en `H-Hv`.
     *
     * @return array{id_nom: int, id_asignatura: int, nombre: string}
     */
    private static function personaConNotaEnDl(): array
    {
        $fila = $GLOBALS['oDBP']->query(
            "SELECT d.id_nom, d.id_asignatura,
                    trim(both ' ' FROM coalesce(g.apellido1, '') || ', ' || coalesce(g.nom, '')) AS nombre
             FROM \"H-dlbv\".e_notas_dl d
             JOIN global.personas g USING (id_nom)
             WHERE NOT EXISTS (SELECT 1 FROM \"H-Hv\".e_notas_otra_region_stgr r WHERE r.id_nom = d.id_nom)
               AND (SELECT count(*) FROM global.personas g2 WHERE g2.id_nom = d.id_nom) = 1
             LIMIT 1"
        )->fetch(\PDO::FETCH_ASSOC);
        if (!is_array($fila)) {
            self::markTestSkipped('No hay ninguna persona con notas en H-dlbv para cruzar');
        }

        return ['id_nom' => (int) $fila['id_nom'], 'id_asignatura' => (int) $fila['id_asignatura'], 'nombre' => (string) $fila['nombre']];
    }

    private static function certificadoEmitido(): ?string
    {
        $numero = $GLOBALS['oDBP']->query(
            "SELECT certificado FROM ONLY \"H-Hv\".e_certificados_rstgr WHERE certificado IS NOT NULL AND certificado <> '' LIMIT 1"
        )->fetchColumn();

        return is_string($numero) ? $numero : null;
    }

    /**
     * @param list<array<string, mixed>> $filas
     * @return array<string, mixed>
     */
    private function fila(array $filas, int $idNom): array
    {
        foreach ($filas as $fila) {
            if ($fila['id_nom'] === $idNom && $fila['esquema'] === self::ESQUEMA) {
                return $fila;
            }
        }
        $this->fail("No aparece el residuo de $idNom");
    }

    public function test_cruza_el_residuo_con_las_notas_de_la_dl_el_nombre_y_los_certificados(): void
    {
        $persona = self::personaConNotaEnDl();
        $certificado = self::certificadoEmitido();
        $this->residuo($persona['id_nom'], $persona['id_asignatura'], $certificado === null ? null : [['certificado' => $certificado]]);
        $this->residuo(self::ID_NOM_INEXISTENTE, 1101, [['certificado' => 'NO EXISTE 0/00']]);

        $consulta = new PgResiduoOtraRegionConsulta();
        $filas = $consulta->listar();

        $conocida = $this->fila($filas, $persona['id_nom']);
        $this->assertSame($persona['id_asignatura'], $conocida['id_asignatura']);
        $this->assertSame($persona['nombre'], $conocida['nombre']);
        $this->assertTrue($conocida['hay_acta_dl'], 'la persona ya tiene esa asignatura en e_notas_dl');
        $this->assertSame(3, $conocida['id_situacion']);
        $this->assertSame(1, $conocida['tipo_acta']);
        $this->assertSame('dlb 30/50', $conocida['acta']);
        $this->assertSame('2025-10-23', $conocida['f_acta']);
        $this->assertSame('test_detalle_vo', $conocida['detalle']);
        if ($certificado !== null) {
            $this->assertSame([$certificado], $conocida['certificados']);
            $this->assertTrue($conocida['certificado_en_modulo']);
        }

        $desconocida = $this->fila($filas, self::ID_NOM_INEXISTENTE);
        $this->assertSame('', $desconocida['nombre']);
        $this->assertFalse($desconocida['hay_acta_dl']);
        $this->assertSame(['NO EXISTE 0/00'], $desconocida['certificados']);
        $this->assertFalse($desconocida['certificado_en_modulo']);

        $this->assertNotContains(self::ESQUEMA, $consulta->esquemasSinPermiso());
    }

    public function test_ordena_por_persona_y_asignatura_y_sin_certificados_da_lista_vacia(): void
    {
        $this->residuo(self::ID_NOM_INEXISTENTE, 2101, null);
        $this->residuo(self::ID_NOM_INEXISTENTE, 1101, null);

        $filas = array_values(array_filter(
            (new PgResiduoOtraRegionConsulta())->listar(),
            static fn (array $f): bool => $f['id_nom'] === self::ID_NOM_INEXISTENTE
        ));

        $this->assertSame([1101, 2101], array_column($filas, 'id_asignatura'));
        $this->assertSame([[], []], array_column($filas, 'certificados'));
    }
}
