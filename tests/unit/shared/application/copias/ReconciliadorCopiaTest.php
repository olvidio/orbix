<?php

declare(strict_types=1);

namespace Tests\unit\shared\application\copias;

use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use src\shared\application\copias\ReconciliadorCopia;
use src\shared\domain\copias\DefinicionCopia;
use src\shared\infrastructure\persistence\copias\ContextoCopia;
use src\shared\infrastructure\persistence\copias\CopiaWriter;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use src\utils_database\domain\entity\DbSchema;

/**
 * Motor de reconciliación de una copia: qué esquemas recorre, cómo calcula
 * altas/cambios/bajas, cuándo escribe y qué pasa cuando algo falla.
 *
 * Se prueba con un reconciliador de mentira que no abre conexiones: las dos
 * bases son PDO simulados y el origen de cada esquema se fija desde el test.
 */
final class ReconciliadorCopiaTest extends TestCase
{
    private DbSchemaRepositoryInterface&MockObject $schemas;
    private CopiaWriter&MockObject $writer;
    private PDO&MockObject $pdoComun;
    private PDO&MockObject $pdoOrigen;
    /** @var list<string> esquemas que existen en cada base */
    private array $enComun = ['H-dlb', 'H-dlc'];
    /** @var list<string> */
    private array $enOrigen = ['H-dlbv', 'H-dlcv'];
    /** @var list<string> operaciones de escritura y de transacción, en orden */
    private array $traza = [];
    /** @var array<string, array<int, array<string, mixed>>|false> copia actual por esquema */
    private array $destino = [];

    protected function setUp(): void
    {
        $this->schemas = $this->createMock(DbSchemaRepositoryInterface::class);
        $this->writer = $this->createMock(CopiaWriter::class);
        $this->traza = [];
        $this->destino = [];

        $this->pdoComun = $this->pdoQueConoce(fn (): array => $this->enComun);
        $this->pdoOrigen = $this->pdoQueConoce(fn (): array => $this->enOrigen);
        $enTransaccion = false;
        $this->pdoComun->method('beginTransaction')->willReturnCallback(function () use (&$enTransaccion): bool {
            $this->traza[] = 'begin';
            $enTransaccion = true;

            return true;
        });
        $this->pdoComun->method('commit')->willReturnCallback(function () use (&$enTransaccion): bool {
            $this->traza[] = 'commit';
            $enTransaccion = false;

            return true;
        });
        $this->pdoComun->method('rollBack')->willReturnCallback(function () use (&$enTransaccion): bool {
            $this->traza[] = 'rollback';
            $enTransaccion = false;

            return true;
        });
        $this->pdoComun->method('inTransaction')->willReturnCallback(function () use (&$enTransaccion): bool {
            return $enTransaccion;
        });

        $this->writer->method('definicion')->willReturn(new DefinicionCopia('copia_x', 'id', ['id', 'nombre', 'activo'], ['activo']));
        $this->writer->method('getErrorTxt')->willReturn('fallo sql');
        $this->writer->method('filasActuales')->willReturnCallback(fn (ContextoCopia $c): array|false => $this->destino[$c->esquema] ?? []);
        $this->writer->method('clavesDeOtroEsquema')->willReturn([]);
        $this->writer->method('upsert')->willReturnCallback(function (ContextoCopia $c, array $fila): bool {
            $this->traza[] = 'upsert ' . $c->esquema . ' ' . $fila['id'];

            return true;
        });
        $this->writer->method('eliminarVarios')->willReturnCallback(function (ContextoCopia $c, array $claves): int {
            $this->traza[] = 'delete ' . $c->esquema . ' ' . implode(',', $claves);

            return count($claves);
        });

        $this->catalogo(['H-dlb' => 3004, 'H-dlc' => 3005]);
    }

    /** PDO simulado cuyo catálogo `pg_namespace` contiene los esquemas dados. */
    private function pdoQueConoce(callable $esquemas): PDO&MockObject
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturnCallback(function () use ($esquemas): PDOStatement {
            $existe = false;
            $stmt = $this->createMock(PDOStatement::class);
            $stmt->method('execute')->willReturnCallback(function (?array $params) use (&$existe, $esquemas): bool {
                $existe = in_array($params[0] ?? null, $esquemas(), true);

                return true;
            });
            $stmt->method('fetchColumn')->willReturnCallback(function () use (&$existe) {
                return $existe ? 1 : false;
            });

            return $stmt;
        });

        return $pdo;
    }

    /** @param array<string, int> $esquemas esquema => id en `db_idschema` */
    private function catalogo(array $esquemas): void
    {
        $lista = [];
        foreach ($esquemas as $nombre => $id) {
            $s = $this->createMock(DbSchema::class);
            $s->method('getSchema')->willReturn($nombre);
            $s->method('getId')->willReturn($id);
            $lista[] = $s;
        }
        $this->schemas = $this->createMock(DbSchemaRepositoryInterface::class);
        $this->schemas->method('matching')->willReturn($lista);
    }

    /**
     * @param array<string, array<int, array<string, mixed>>|\Throwable> $origen filas que deberían estar en la copia, por esquema de origen
     * @param array<string, string> $omitir motivo de omisión por esquema de comun
     */
    private function reconciliador(array $origen = [], bool $bajasPrimero = false, array $omitir = []): ReconciliadorCopia
    {
        $pdoComun = $this->pdoComun;
        $pdoOrigen = $this->pdoOrigen;

        return new class($this->schemas, $this->writer, $pdoComun, $pdoOrigen, $origen, $bajasPrimero, $omitir) extends ReconciliadorCopia {
            /** @var list<string> */
            public array $conexiones = [];

            /**
             * @param array<string, array<int, array<string, mixed>>|\Throwable> $origen
             * @param array<string, string> $omitir
             */
            public function __construct(
                DbSchemaRepositoryInterface $schemas,
                CopiaWriter $writer,
                private PDO $comun,
                private PDO $origenPdo,
                private array $origen,
                private bool $bajasPrimero,
                private array $omitir,
            ) {
                parent::__construct($schemas, $writer);
            }

            protected function claveEsquemaOrigen(): string
            {
                return 'publicv';
            }

            protected function nombreBaseOrigen(): string
            {
                return 'sv';
            }

            protected function contextoDe(PDO $pdoComun, string $esquema, int $id_schema): ContextoCopia|string
            {
                if (isset($this->omitir[$esquema])) {
                    return $this->omitir[$esquema];
                }

                return new class($pdoComun, $esquema, '', $id_schema) extends ContextoCopia {
                    public function nombreTabla(): string
                    {
                        return 'copia_x';
                    }
                };
            }

            protected function leerOrigen(PDO $pdoOrigen, string $esquemaOrigen, ContextoCopia $contexto): array
            {
                $filas = $this->origen[$esquemaOrigen] ?? [];
                if ($filas instanceof \Throwable) {
                    throw $filas;
                }

                return $filas;
            }

            protected function bajasAntesDeAltas(): bool
            {
                return $this->bajasPrimero;
            }

            protected function conectar(string $claveEsquema): PDO
            {
                $this->conexiones[] = $claveEsquema;

                return $claveEsquema === 'public' ? $this->comun : $this->origenPdo;
            }
        };
    }

    /** @return array<string, mixed> */
    private static function fila(int $id, string $nombre = 'x', bool $activo = true): array
    {
        return ['id' => $id, 'nombre' => $nombre, 'activo' => $activo];
    }

    /**
     * @param array<string, mixed> $resultado
     * @return array<string, mixed>
     */
    private static function informeDe(array $resultado, string $esquema): array
    {
        foreach ($resultado['esquemas'] as $informe) {
            if ($informe['esquema'] === $esquema) {
                return $informe;
            }
        }
        self::fail('no hay informe de ' . $esquema);
    }

    // ---- conexiones y esquemas que se recorren ---------------------------------------------------------

    public function test_abre_la_base_comun_y_la_de_origen_que_declara_la_copia(): void
    {
        $r = $this->reconciliador();
        $r->execute();

        $this->assertSame(['public', 'publicv'], $r->conexiones);
    }

    public function test_solo_recorre_los_esquemas_de_dl_de_comun(): void
    {
        $this->catalogo([
            'H-dlb' => 3004,
            'H-dlbv' => 3104,     // interior: termina en v
            'H-dlbf' => 3204,     // exterior: termina en f
            'resto' => 3001,
            'H-H' => 3002,        // raíz de región STGR
            'M-M' => 3003,
            'public' => 1,        // id fuera de 3000-3999
            'H-dlz' => 4000,      // id fuera de 3000-3999
            'H-dly' => 2999,
            'H-dlc' => 3999,
        ]);
        $this->enComun = ['H-dlb', 'H-dlc', 'H-dlbv', 'H-dlbf', 'resto', 'H-H', 'M-M', 'public', 'H-dlz', 'H-dly'];

        $res = $this->reconciliador()->execute();

        $this->assertSame(['H-dlb', 'H-dlc'], array_column($res['esquemas'], 'esquema'));
        $this->assertSame(2, $res['totales']['esquemas']);
    }

    public function test_con_un_esquema_concreto_solo_se_reconcilia_ese(): void
    {
        $res = $this->reconciliador()->execute(false, 'H-dlc');

        $this->assertSame(['H-dlc'], array_column($res['esquemas'], 'esquema'));
    }

    public function test_un_esquema_concreto_que_no_esta_en_el_catalogo_lanza_excepcion(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('El esquema "H-dlx" no está en el catálogo de comun');

        $this->reconciliador()->execute(false, 'H-dlx');
    }

    /** Un esquema interior tampoco vale como «solo este»: el catálogo que cuenta es el de comun. */
    public function test_un_esquema_interior_no_se_acepta_como_esquema_concreto(): void
    {
        $this->catalogo(['H-dlb' => 3004, 'H-dlbv' => 3104]);

        $this->expectException(RuntimeException::class);
        $this->reconciliador()->execute(false, 'H-dlbv');
    }

    // ---- omisiones -------------------------------------------------------------------------------------

    public function test_se_omite_el_esquema_que_no_existe_en_comun(): void
    {
        $this->enComun = ['H-dlc'];

        $res = $this->reconciliador()->execute();

        $this->assertSame('no existe en comun', self::informeDe($res, 'H-dlb')['omitido']);
        $this->assertSame(1, $res['totales']['esquemas'], 'los omitidos no cuentan');
        $this->assertStringContainsString('omitido (no existe en comun)', $res['lineas'][0]);
    }

    public function test_se_omite_el_esquema_que_no_existe_en_la_base_de_origen(): void
    {
        $this->enOrigen = ['H-dlcv'];

        $res = $this->reconciliador()->execute();

        $this->assertSame('no existe en sv', self::informeDe($res, 'H-dlb')['omitido']);
    }

    public function test_se_omite_el_esquema_para_el_que_la_copia_no_da_contexto(): void
    {
        $this->writer->expects($this->never())->method('filasActuales');

        $res = $this->reconciliador(omitir: ['H-dlb' => 'no se deduce la dl', 'H-dlc' => 'no se deduce la dl'])->execute(true);

        $this->assertSame('no se deduce la dl', self::informeDe($res, 'H-dlb')['omitido']);
        $this->assertSame(['esquemas' => 0, 'altas' => 0, 'cambios' => 0, 'bajas' => 0, 'errores' => 0], $res['totales']);
    }

    // ---- diferencias -----------------------------------------------------------------------------------

    public function test_calcula_altas_cambios_y_bajas_comparando_origen_y_copia(): void
    {
        $this->destino['H-dlb'] = [
            1 => self::fila(1, 'igual'),
            2 => self::fila(2, 'viejo'),
            3 => self::fila(3, 'sobra'),
        ];
        $origen = ['H-dlbv' => [
            1 => self::fila(1, 'igual'),
            2 => self::fila(2, 'nuevo', false),
            4 => self::fila(4, 'alta'),
        ]];

        $res = $this->reconciliador($origen)->execute(false, 'H-dlb');

        $informe = self::informeDe($res, 'H-dlb');
        $this->assertSame([3, 3, 1, 1, 1], [$informe['origen'], $informe['destino'], $informe['altas'], $informe['cambios'], $informe['bajas']]);
        $this->assertSame(['2: nombre,activo'], $informe['detalle'], 'el detalle dice qué columnas difieren');
        $this->assertSame(['esquemas' => 1, 'altas' => 1, 'cambios' => 1, 'bajas' => 1, 'errores' => 0], $res['totales']);
    }

    /** Sin esto, cada pasada reescribiría filas idénticas y generaría ruido en la replicación. */
    public function test_valores_equivalentes_con_distinto_tipo_no_cuentan_como_cambio(): void
    {
        $this->destino['H-dlb'] = [1 => ['id' => '1', 'nombre' => 'a', 'activo' => 't'], 2 => ['id' => '2', 'nombre' => null, 'activo' => 'f']];
        $origen = ['H-dlbv' => [1 => ['id' => 1, 'nombre' => 'a', 'activo' => true], 2 => ['id' => 2, 'nombre' => '', 'activo' => false]]];

        $res = $this->reconciliador($origen)->execute(true, 'H-dlb');

        $this->assertSame(['esquemas' => 1, 'altas' => 0, 'cambios' => 0, 'bajas' => 0, 'errores' => 0], $res['totales']);
        $this->assertSame([], $this->traza, 'en estado estable no se escribe ni se abre transacción');
    }

    public function test_el_detalle_se_corta_a_veinte_filas_pero_el_recuento_es_completo(): void
    {
        $origen = [];
        for ($i = 1; $i <= 25; $i++) {
            $this->destino['H-dlb'][$i] = self::fila($i, 'viejo');
            $origen[$i] = self::fila($i, 'nuevo');
        }

        $informe = self::informeDe($this->reconciliador(['H-dlbv' => $origen])->execute(false, 'H-dlb'), 'H-dlb');

        $this->assertSame(25, $informe['cambios']);
        $this->assertCount(20, $informe['detalle']);
    }

    public function test_las_filas_de_otro_id_schema_no_se_dan_de_baja(): void
    {
        $this->destino['H-dlb'] = [1 => self::fila(1), 8 => self::fila(8), 9 => self::fila(9)];
        $this->writer = $this->createMock(CopiaWriter::class);
        $this->writer->method('definicion')->willReturn(new DefinicionCopia('copia_x', 'id', ['id', 'nombre', 'activo'], ['activo']));
        $this->writer->method('filasActuales')->willReturn($this->destino['H-dlb']);
        $this->writer->method('clavesDeOtroEsquema')->willReturn([8]);
        $borradas = null;
        $this->writer->method('eliminarVarios')->willReturnCallback(function (ContextoCopia $c, array $claves) use (&$borradas): int {
            $borradas = $claves;

            return count($claves);
        });

        $informe = self::informeDe($this->reconciliador(['H-dlbv' => [1 => self::fila(1)]])->execute(true, 'H-dlb'), 'H-dlb');

        $this->assertSame(1, $informe['bajas']);
        $this->assertSame([9], $borradas);
        $this->assertContains('1 fila(s) de otro id_schema respetadas', $informe['detalle']);
    }

    // ---- informe frente a aplicar ----------------------------------------------------------------------

    public function test_en_modo_informe_no_se_escribe_nada(): void
    {
        $this->destino['H-dlb'] = [3 => self::fila(3)];
        $this->writer->expects($this->never())->method('upsert');
        $this->writer->expects($this->never())->method('eliminarVarios');

        $res = $this->reconciliador(['H-dlbv' => [4 => self::fila(4)]])->execute();

        $this->assertFalse($res['aplicado']);
        $this->assertSame([], $this->traza);
        $this->assertStringContainsString('altas=1', $res['lineas'][0]);
        $this->assertStringEndsWith('(informe)', $res['lineas'][0]);
    }

    public function test_al_aplicar_escribe_altas_y_cambios_y_luego_bajas_en_una_transaccion_por_esquema(): void
    {
        $this->destino['H-dlb'] = [2 => self::fila(2, 'viejo'), 3 => self::fila(3)];
        $this->destino['H-dlc'] = [7 => self::fila(7)];
        $origen = [
            'H-dlbv' => [2 => self::fila(2, 'nuevo'), 4 => self::fila(4)],
            'H-dlcv' => [],
        ];

        $res = $this->reconciliador($origen)->execute(true);

        $this->assertTrue($res['aplicado']);
        $this->assertSame(
            ['begin', 'upsert H-dlb 4', 'upsert H-dlb 2', 'delete H-dlb 3', 'commit', 'begin', 'delete H-dlc 7', 'commit'],
            $this->traza
        );
        $this->assertSame(['esquemas' => 2, 'altas' => 1, 'cambios' => 1, 'bajas' => 2, 'errores' => 0], $res['totales']);
        $this->assertStringEndsNotWith('(informe)', $res['lineas'][0]);
    }

    public function test_la_copia_puede_pedir_que_las_bajas_vayan_antes_que_las_altas(): void
    {
        $this->destino['H-dlb'] = [3 => self::fila(3)];

        $this->reconciliador(['H-dlbv' => [4 => self::fila(4)]], bajasPrimero: true)->execute(true, 'H-dlb');

        $this->assertSame(['begin', 'delete H-dlb 3', 'upsert H-dlb 4', 'commit'], $this->traza);
    }

    // ---- errores ---------------------------------------------------------------------------------------

    /** Un origen ilegible no puede confundirse con un origen vacío: borraría la copia entera. */
    public function test_si_el_origen_no_se_puede_leer_el_esquema_queda_en_error_y_no_se_toca(): void
    {
        $this->destino['H-dlb'] = [1 => self::fila(1)];
        $this->writer->expects($this->never())->method('eliminarVarios');
        $origen = ['H-dlbv' => new RuntimeException('no existe H-dlbv.tabla en sv'), 'H-dlcv' => [5 => self::fila(5)]];

        $res = $this->reconciliador($origen)->execute(true);

        $this->assertSame('no existe H-dlbv.tabla en sv', self::informeDe($res, 'H-dlb')['error']);
        $this->assertSame(['esquemas' => 2, 'altas' => 1, 'cambios' => 0, 'bajas' => 0, 'errores' => 1], $res['totales'], 'el error de un esquema no detiene los demás');
        $this->assertStringContainsString('ERROR: no existe H-dlbv.tabla en sv', $res['lineas'][0]);
        $this->assertSame(['begin', 'upsert H-dlc 5', 'commit'], $this->traza);
    }

    public function test_si_la_copia_no_se_puede_leer_el_esquema_queda_en_error(): void
    {
        $this->destino['H-dlb'] = false;

        $res = $this->reconciliador(['H-dlbv' => [1 => self::fila(1)]])->execute(true, 'H-dlb');

        $this->assertSame('no se ha podido leer copia_x', self::informeDe($res, 'H-dlb')['error']);
        $this->assertSame([], $this->traza);
    }

    public function test_si_falla_una_escritura_se_deshace_todo_el_esquema_y_se_informa_el_error(): void
    {
        $this->destino['H-dlb'] = [3 => self::fila(3)];
        $this->writer = $this->createMock(CopiaWriter::class);
        $this->writer->method('definicion')->willReturn(new DefinicionCopia('copia_x', 'id', ['id', 'nombre', 'activo'], ['activo']));
        $this->writer->method('filasActuales')->willReturn($this->destino['H-dlb']);
        $this->writer->method('clavesDeOtroEsquema')->willReturn([]);
        $this->writer->method('getErrorTxt')->willReturn('fallo sql');
        $this->writer->method('upsert')->willReturn(false);
        $this->writer->expects($this->never())->method('eliminarVarios');

        $res = $this->reconciliador(['H-dlbv' => [4 => self::fila(4)]])->execute(true, 'H-dlb');

        $informe = self::informeDe($res, 'H-dlb');
        $this->assertSame('upsert: fallo sql', $informe['error']);
        $this->assertSame(0, $informe['altas'], 'un esquema con error no aporta recuentos');
        $this->assertSame(['begin', 'rollback'], $this->traza);
        $this->assertSame(1, $res['totales']['errores']);
    }

    public function test_si_falla_el_borrado_tambien_se_deshace(): void
    {
        $this->destino['H-dlb'] = [3 => self::fila(3)];
        $this->writer = $this->createMock(CopiaWriter::class);
        $this->writer->method('definicion')->willReturn(new DefinicionCopia('copia_x', 'id', ['id', 'nombre', 'activo'], ['activo']));
        $this->writer->method('filasActuales')->willReturn($this->destino['H-dlb']);
        $this->writer->method('clavesDeOtroEsquema')->willReturn([]);
        $this->writer->method('getErrorTxt')->willReturn('fallo sql');
        $this->writer->method('eliminarVarios')->willReturn(false);

        $res = $this->reconciliador(['H-dlbv' => []])->execute(true, 'H-dlb');

        $this->assertSame('delete: fallo sql', self::informeDe($res, 'H-dlb')['error']);
        $this->assertSame(['begin', 'rollback'], $this->traza);
    }
}
