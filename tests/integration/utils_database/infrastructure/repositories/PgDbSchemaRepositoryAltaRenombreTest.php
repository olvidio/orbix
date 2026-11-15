<?php

declare(strict_types=1);

namespace Tests\integration\utils_database\infrastructure\repositories;

use src\utils_database\infrastructure\persistence\postgresql\PgDbSchemaRepository;
use Tests\myTest;

/**
 * Registro de ids de esquema (`public.db_idschema` de comun): dar de alta un esquema nuevo
 * (`llenarNuevo`: id siguiente del rango 3000-4000 y sus variantes sf = id-1000 y sv = id-2000) y
 * cambiarle el nombre (`cambiarNombre`: idempotente, y si ya existe el nombre nuevo borra el antiguo).
 *
 * Usa un esquema de prueba y borra sus filas al final. Instancia propia del repositorio: `llenarNuevo`
 * cambia la conexión del objeto y no debe tocar la del contenedor.
 */
final class PgDbSchemaRepositoryAltaRenombreTest extends myTest
{
    private const VIEJO = 'Zzt-altae2e';
    private const NUEVO = 'Zzt-renombre2e';

    private PgDbSchemaRepository $repo;

    public function setUp(): void
    {
        parent::setUp();
        $this->repo = new PgDbSchemaRepository();
        $this->borrar();
    }

    public function tearDown(): void
    {
        $this->borrar();
        parent::tearDown();
    }

    private function borrar(): void
    {
        $GLOBALS['oDBPC']->exec("DELETE FROM public.db_idschema WHERE schema LIKE 'Zzt-%'");
        // El registro de sv (lo usa `test_tras_registrar_en_sv_el_alta_en_comun_va_a_comun`).
        $GLOBALS['oDBP']->exec("DELETE FROM public.db_idschema WHERE schema LIKE 'Zzt-%'");
    }

    /**
     * El registro de un esquema se lleva en comun, sv y sv-e con la misma instancia del repositorio.
     * Tras un alta en sv la conexión se quedaba en sv, y el siguiente alta «en comun» iba a parar al
     * registro de sv (dos altas de esquema en el mismo proceso).
     */
    public function test_tras_registrar_en_sv_el_alta_en_comun_va_a_comun(): void
    {
        $this->repo->llenarNuevo(self::VIEJO, 'comun');
        $this->repo->llenarNuevo(self::VIEJO, 'sv');
        $this->repo->llenarNuevo(self::NUEVO, 'comun');

        $this->assertArrayHasKey(self::NUEVO, $this->filas(), 'el segundo esquema está en el registro de comun');
        $enSv = (int) $GLOBALS['oDBP']->query("SELECT count(*) FROM public.db_idschema WHERE schema LIKE '" . self::NUEVO . "%'")->fetchColumn();
        $this->assertSame(0, $enSv, 'y no en el de sv');
    }

    /** @return array<string, int> schema => id */
    private function filas(): array
    {
        $st = $GLOBALS['oDBPC']->query("SELECT schema, id FROM public.db_idschema WHERE schema LIKE 'Zzt-%' ORDER BY schema");
        $out = [];
        foreach ($st->fetchAll(\PDO::FETCH_ASSOC) as $f) {
            $out[$f['schema']] = (int) $f['id'];
        }
        return $out;
    }

    /**
     * Después de un alta o de un cambio de nombre en sv, el repositorio vuelve a trabajar con comun:
     * si se quedaba en sv, cualquier `Guardar` posterior del mismo proceso iba al registro de sv.
     */
    public function test_tras_un_alta_en_sv_el_repositorio_vuelve_a_comun(): void
    {
        $this->repo->llenarNuevo(self::VIEJO, 'comun');
        $this->repo->llenarNuevo(self::VIEJO, 'sv');
        $this->repo->cambiarNombre(self::VIEJO, self::NUEVO, 'sv');

        $directo = new \src\utils_database\domain\entity\DbSchema();
        $directo->setIdVo(new \src\utils_database\domain\value_objects\DbSchemaId(3999));
        $directo->setSchemaVo(\src\utils_database\domain\value_objects\DbSchemaCode::fromString('Zzt-directo'));
        $this->repo->Guardar($directo);

        $this->assertSame(3999, $this->filas()['Zzt-directo'] ?? null, 'el guardado va al registro de comun');
    }

    private function ultimoDelRango(): int
    {
        return (int) $GLOBALS['oDBPC']->query('SELECT max(id) FROM public.db_idschema WHERE id BETWEEN 3000 AND 4000')->fetchColumn();
    }

    public function test_alta_de_un_esquema_con_sus_variantes_sf_y_sv(): void
    {
        $id = $this->ultimoDelRango() + 1;

        $this->repo->llenarNuevo(self::VIEJO, 'comun');

        $this->assertSame([self::VIEJO => $id, self::VIEJO . 'f' => $id - 1000, self::VIEJO . 'v' => $id - 2000], $this->filas());

        $this->repo->llenarNuevo(self::VIEJO, 'comun');
        $this->assertSame([self::VIEJO => $id, self::VIEJO . 'f' => $id - 1000, self::VIEJO . 'v' => $id - 2000], $this->filas(), 'si ya existe, conserva su id');
    }

    public function test_cambiar_el_nombre_conserva_los_ids_y_es_idempotente(): void
    {
        $this->repo->llenarNuevo(self::VIEJO, 'comun');
        $antes = array_values($this->filas());

        $this->repo->cambiarNombre(self::VIEJO, self::NUEVO, 'comun');
        $this->assertSame([self::NUEVO, self::NUEVO . 'f', self::NUEVO . 'v'], array_keys($this->filas()));
        $this->assertSame($antes, array_values($this->filas()));

        $this->repo->cambiarNombre(self::VIEJO, self::NUEVO, 'comun');
        $this->assertSame($antes, array_values($this->filas()), 'repetirlo no cambia nada');
    }

    public function test_si_ya_existe_el_nombre_nuevo_se_borra_el_antiguo(): void
    {
        $this->repo->llenarNuevo(self::VIEJO, 'comun');
        $this->repo->llenarNuevo(self::NUEVO, 'comun');
        $idNuevo = $this->filas()[self::NUEVO];

        $this->repo->cambiarNombre(self::VIEJO, self::NUEVO, 'comun');

        $this->assertSame([self::NUEVO, self::NUEVO . 'f', self::NUEVO . 'v'], array_keys($this->filas()));
        $this->assertSame($idNuevo, $this->filas()[self::NUEVO]);
    }

    public function test_base_de_datos_no_soportada(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->repo->cambiarNombre(self::VIEJO, self::NUEVO, 'otra');
    }
}
