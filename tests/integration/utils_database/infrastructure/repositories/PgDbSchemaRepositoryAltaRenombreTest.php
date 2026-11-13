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
