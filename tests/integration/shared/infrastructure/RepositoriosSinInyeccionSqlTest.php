<?php

declare(strict_types=1);

namespace Tests\integration\shared\infrastructure;

use PHPUnit\Framework\Attributes\DataProvider;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\menus\domain\contracts\TemplateMenuRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\infrastructure\GlobalPdo;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\TipoCasaRepositoryInterface;
use src\ubis\domain\contracts\TipoCentroRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use Tests\myTest;

/**
 * Valores que llegan de la petición y acababan dentro de un literal SQL (`'$x'`). Con la carga
 * `x' OR '1'='1` la condición interpolada es siempre cierta y la consulta devolvía filas; con el valor
 * enlazado como parámetro no encuentra nada. Solo lee.
 *
 * Revisión de seguridad del 2026-11-18 (plan de tests, sección 38). La guardia estática es
 * `Tests\unit\shared\infrastructure\SqlSinValoresInterpoladosTest`.
 */
final class RepositoriosSinInyeccionSqlTest extends myTest
{
    private const CARGA = "x' OR '1'='1";

    private function repo(string $interfaz): object
    {
        return $GLOBALS['container']->get($interfaz);
    }

    /** @return iterable<string, array{string, string}> */
    public static function clavesDeTexto(): iterable
    {
        // Se llega a todos desde la pantalla genérica de tablas (`tablaDB_formulario_datos`, `a_pkey`).
        yield 'acta' => [ActaRepositoryInterface::class, 'findById'];
        yield 'tipo de centro' => [TipoCentroRepositoryInterface::class, 'findById'];
        yield 'tipo de casa' => [TipoCasaRepositoryInterface::class, 'findById'];
        yield 'idioma' => [LocalRepositoryInterface::class, 'findById'];
        yield 'situación' => [SituacionRepositoryInterface::class, 'findById'];
        yield 'parámetro de configuración' => [ConfigSchemaRepositoryInterface::class, 'findById'];
        yield 'parámetro de la pasarela' => [PasarelaConfigRepositoryInterface::class, 'findById'];
        yield 'correo en cola' => [ColaMailRepositoryInterface::class, 'findById'];
        yield 'esquema' => [DbSchemaRepositoryInterface::class, 'findById'];
        yield 'plantilla de menús' => [TemplateMenuRepositoryInterface::class, 'findByName'];
    }

    #[DataProvider('clavesDeTexto')]
    public function test_una_clave_de_texto_con_comillas_no_encuentra_nada(string $interfaz, string $metodo): void
    {
        $this->assertNull($this->repo($interfaz)->$metodo(self::CARGA));
    }

    #[DataProvider('clavesDeTexto')]
    public function test_datos_por_id_con_comillas_no_encuentra_nada(string $interfaz, string $metodo): void
    {
        if ($metodo !== 'findById' || !method_exists($this->repo($interfaz), 'datosById')) {
            $this->markTestSkipped('sin datosById');
        }
        $this->assertFalse($this->repo($interfaz)->datosById(self::CARGA));
    }

    /** Preferencias de tabla (`preferencias_guardar`, `preferencia_tabla_get`: el tipo lleva el nombre de la tabla). */
    public function test_preferencia_con_comillas_en_el_tipo(): void
    {
        $st = GlobalPdo::get('oDBE_Select')->query('SELECT id_usuario FROM web_preferencias LIMIT 1');
        $idUsuario = $st === false ? false : $st->fetchColumn();
        if ($idUsuario === false) {
            $this->markTestIncomplete('En las BD de pruebas no hay preferencias');
        }

        $this->assertNull($this->repo(PreferenciaRepositoryInterface::class)->findById((int) $idUsuario, self::CARGA));
    }

    /** Buscador de asignaturas (`asignaturas_search`). */
    public function test_busqueda_de_asignaturas_con_comillas(): void
    {
        $json = $this->repo(AsignaturaRepositoryInterface::class)->getJsonAsignaturas(['nombre_asignatura' => "zzqq%' OR 1=1 --"]);

        $this->assertSame('[]', $json);
    }

    /** Tipos de proceso de un tipo de actividad (`fases_activ_cambio_get`, `usuario_perm_activ_ajax`). */
    public function test_tipos_de_proceso_con_comillas(): void
    {
        $repo = $this->repo(TipoDeActividadRepositoryInterface::class);

        $this->assertSame([], $repo->getTiposDeProcesos(self::CARGA, true, 'sv'));
        $this->assertSame([], $repo->getTiposDeProcesos(self::CARGA, false, 'sv'));
    }

    /** Descripciones de teleco según el tipo (`tablaDB_depende_datos`, `valor_depende`). */
    public function test_descripciones_de_teleco_con_comillas(): void
    {
        $repo = $this->repo(DescTelecoRepositoryInterface::class);

        $this->assertSame([], $repo->getArrayDescTelecoPersonas(self::CARGA));
        $this->assertSame([], $repo->getArrayDescTelecoUbis(self::CARGA));
    }
}
