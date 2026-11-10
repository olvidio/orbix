<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PDO;
use PDOException;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use src\personas\application\PersonaPublicarFormData;
use src\personas\application\ResincronizarCpSacd;
use src\personas\application\services\PersonaFinderService;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaN;
use src\personas\infrastructure\persistence\postgresql\CpSacdWriter;
use src\shared\infrastructure\persistence\copias\ContextoCopia;
use src\shared\security\HashB;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use Tests\myTest;

/** Formulario de publicación de personas y lectura del origen de la copia `cp_sacd`. */
final class PersonaPublicarYCpSacdTest extends myTest
{
    private PersonaNRepositoryInterface&MockObject $repoN;
    private PersonaFinderService&MockObject $finder;
    private PersonaPublicarFormData $form;
    private mixed $sesionAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['esquema' => 'H-dlbv'];
        $this->repoN = $this->createMock(PersonaNRepositoryInterface::class);
        $resolver = new PersonaRepositoryResolver(
            $this->repoN,
            $this->createMock(PersonaAgdRepositoryInterface::class),
            $this->createMock(PersonaNaxRepositoryInterface::class),
            $this->createMock(PersonaSRepositoryInterface::class),
            $this->createMock(PersonaSSSCRepositoryInterface::class),
            $this->createMock(PersonaExRepositoryInterface::class),
            $this->createMock(PersonaDlRepositoryInterface::class),
            $this->createMock(PersonaSacdRepositoryInterface::class),
        );
        $this->finder = $this->createMock(PersonaFinderService::class);
        $this->form = new PersonaPublicarFormData($resolver, $this->finder);
    }

    public function tearDown(): void
    {
        if ($this->sesionAnterior === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->sesionAnterior;
        }
        parent::tearDown();
    }

    private function persona(string $nombre, int $schema): PersonaN&MockObject
    {
        $p = $this->createMock(PersonaN::class);
        $p->method('getNombreApellidos')->willReturn($nombre);
        $p->method('getId_schema')->willReturn($schema);
        $p->method('getId_tabla')->willReturn('n');

        return $p;
    }

    // ---- PersonaPublicarFormData ---------------------------------------------------------------

    public function test_sin_id_valido_avisa_que_no_se_encuentra_la_persona(): void
    {
        $this->assertArrayHasKey('error', $this->form->execute([]));
        $this->assertArrayHasKey('error', $this->form->execute(['id_nom' => '0']));
    }

    public function test_una_persona_inexistente_en_global_avisa(): void
    {
        $this->repoN->method('findById')->willReturn(null);
        $this->finder->method('findPersonaEnGlobal')->willReturn(null);

        $this->assertArrayHasKey('error', $this->form->execute(['id_nom' => '10011', 'id_tabla' => 'n']));
    }

    public function test_una_persona_sin_esquema_no_se_puede_publicar(): void
    {
        $this->repoN->method('findById')->willReturn($this->persona('Ana Gil', 0));

        $r = $this->form->execute(['id_nom' => '10011', 'id_tabla' => 'n']);

        $this->assertArrayHasKey('error', $r);
    }

    public function test_con_tabla_conocida_usa_su_repositorio_y_firma_con_id_y_esquema(): void
    {
        $this->repoN->method('findById')->with(10011)->willReturn($this->persona('Ana Gil', 12));
        $this->finder->expects($this->never())->method('findPersonaEnGlobal');

        $r = $this->form->execute(['id_nom' => '10011', 'id_tabla' => 'n']);

        $this->assertSame(['Ana Gil', 10011, 'n', 12], [$r['nom'], $r['id_nom'], $r['id_tabla'], $r['id_schema']]);
        $this->assertSame(['id_nom' => 10011, 'id_schema' => 12], HashB::open($r['ctx_publicar'], 'persona_publicar'));
        $this->assertIsArray($r['opciones_dl']);
        $this->assertArrayNotHasKey('dlb', $r['opciones_dl'], 'la dl propia no se ofrece');
    }

    public function test_una_tabla_desconocida_cae_a_la_busqueda_global_y_la_seleccion_manda_sobre_los_campos(): void
    {
        $this->finder->method('findPersonaEnGlobal')->with(10012)->willReturn($this->persona('Luis Paz', 5));

        $r = $this->form->execute(['sel' => ['10012#zz'], 'id_nom' => '1']);

        $this->assertSame([10012, 'zz', 'Luis Paz'], [$r['id_nom'], $r['id_tabla'], $r['nom']]);
    }

    public function test_sin_tabla_se_toma_la_de_la_persona_encontrada_y_la_seleccion_puede_ser_texto(): void
    {
        $this->finder->method('findPersonaEnGlobal')->willReturn($this->persona('Luis Paz', 5));

        $r = $this->form->execute(['sel' => '10012#']);

        $this->assertSame([10012, 'n'], [$r['id_nom'], $r['id_tabla']]);
    }

    // ---- ResincronizarCpSacd::leerOrigen -----------------------------------------------------

    private function recon(): ResincronizarCpSacd
    {
        return new ResincronizarCpSacd($this->createMock(DbSchemaRepositoryInterface::class), new CpSacdWriter());
    }

    /** @return array<string, mixed> */
    private function registro(int $id, string $tabla, string $dl, bool $sacd = true): array
    {
        return ['id_nom' => $id, 'id_tabla' => $tabla, 'dl' => $dl, 'sacd' => $sacd ? 't' : 'f', 'nom' => 'X'];
    }

    /**
     * @param list<list<array<string, mixed>>|PDOException> $respuestas una por consulta, en orden
     * @param list<string> $sqls recoge el SQL preparado
     */
    private function pdo(array $respuestas, array &$sqls = []): PDO&MockObject
    {
        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturnCallback(function (string $sql) use (&$respuestas, &$sqls): PDOStatement {
            $sqls[] = $sql;
            $r = array_shift($respuestas);
            $st = $this->createMock(PDOStatement::class);
            if ($r instanceof PDOException) {
                $st->method('execute')->willThrowException($r);
            } else {
                $st->method('fetchAll')->willReturn($r ?? []);
            }

            return $st;
        });

        return $pdo;
    }

    /** @return array<int, array<string, mixed>> */
    private function leerOrigen(PDO $pdo): array
    {
        $m = new \ReflectionMethod(ResincronizarCpSacd::class, 'leerOrigen');

        return $m->invoke($this->recon(), $pdo, 'H-dlbv', $this->createMock(ContextoCopia::class));
    }

    public function test_el_origen_une_las_tres_tablas_de_la_dl_y_los_de_paso_otra_y_descarta_el_resto(): void
    {
        $sqls = [];
        $pdo = $this->pdo([
            [$this->registro(1, 'n', 'dlb'), $this->registro(2, 'n', 'dlb', false)],
            [$this->registro(3, 'a', 'dlb')],
            [],
            [$this->registro(-4, 'pn', 'Otra'), $this->registro(-5, 'pn', 'dlx')],
        ], $sqls);

        $filas = $this->leerOrigen($pdo);

        $this->assertSame([1, 3, -4], array_keys($filas));
        $this->assertCount(4, $sqls);
        $this->assertStringContainsString('"H-dlbv"."p_numerarios"', $sqls[0]);
        $this->assertStringContainsString('"p_sssc"', $sqls[2]);
        $this->assertStringContainsString('"restov"."p_de_paso_ex"', $sqls[3]);
        $this->assertStringContainsString('NULL AS id_ctr', $sqls[3]);
        $this->assertStringContainsString('AND dl = :dl', $sqls[3]);
        $this->assertStringNotContainsString('NULL AS id_ctr', $sqls[0]);
    }

    public function test_una_tabla_inexistente_se_toma_como_origen_vacio(): void
    {
        $e = new PDOException('no existe');
        $e->errorInfo = ['42P01'];
        $this->setProp($e, 'code', '42P01');
        $pdo = $this->pdo([$e, $e, $e, $e]);

        $this->assertSame([], $this->leerOrigen($pdo));
    }

    public function test_cualquier_otro_error_de_bd_se_propaga_para_no_borrar_la_copia(): void
    {
        $e = new PDOException('caída');
        $this->setProp($e, 'code', '08006');
        $pdo = $this->pdo([$e]);

        $this->expectException(PDOException::class);
        $this->leerOrigen($pdo);
    }

    public function test_los_hooks_de_la_copia_identifican_origen_y_deducen_la_dl(): void
    {
        $r = $this->recon();
        $get = fn (string $m): mixed => (new \ReflectionMethod($r, $m))->invoke($r);

        $this->assertSame(['publicv', 'sv'], [$get('claveEsquemaOrigen'), $get('nombreBaseOrigen')]);

        $ctx = new \ReflectionMethod($r, 'contextoDe');
        $this->assertIsString($ctx->invoke($r, $this->createMock(PDO::class), 'singuion', 1));
    }

    private function setProp(\Throwable $e, string $prop, mixed $valor): void
    {
        $rp = new \ReflectionProperty(\Exception::class, $prop);
        $rp->setValue($e, $valor);
    }
}
