<?php

namespace Tests\integration\permisos\domain;

use PDO;
use src\permisos\domain\PermisosActividades;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\GlobalPdo;
use Tests\myTest;

/**
 * Carga real de la matriz de permisos (`aux_usuarios_perm`) con la prioridad
 * usuario > grupo y grupos por orden alfabético (A-Z). Usa un usuario y dos grupos de prueba
 * con ids altos (4990001, 5990001 y 5990002) y borra todo al terminar.
 */
class PermisosActividadesCargaTest extends myTest
{
    private const USUARIO = 4990001;
    private const GRUPO_A = 5990001;
    private const GRUPO_B = 5990002;
    private const GRUPO_AJENO = 5990003;

    private const D = PermisosActividades::AFECTA['datos'];
    private const SACD = PermisosActividades::AFECTA['sacd'];

    private PDO $pdo;

    public function setUp(): void
    {
        parent::setUp();
        $this->pdo = GlobalPdo::get('oDBE');
        $this->limpiar();
        $this->pdo->exec("INSERT INTO aux_usuarios (id_usuario, usuario) VALUES (" . self::USUARIO . ", 'tstu_perm')");
        $this->pdo->exec(
            "INSERT INTO aux_grupos_y_usuarios (id_usuario, usuario) VALUES
             (" . self::GRUPO_A . ", 'tstg_a'), (" . self::GRUPO_B . ", 'tstg_b'), (" . self::GRUPO_AJENO . ", 'tstg_ajeno')"
        );
        $this->pdo->exec(
            "INSERT INTO aux_cross_usuarios_grupos (id_usuario, id_grupo) VALUES
             (" . self::USUARIO . ", " . self::GRUPO_A . "), (" . self::USUARIO . ", " . self::GRUPO_B . ")"
        );
    }

    public function tearDown(): void
    {
        $this->limpiar();
        parent::tearDown();
    }

    private function limpiar(): void
    {
        $ids = implode(',', [self::USUARIO, self::GRUPO_A, self::GRUPO_B, self::GRUPO_AJENO]);
        $this->pdo->exec("DELETE FROM aux_usuarios_perm WHERE id_usuario IN ($ids)");
        $this->pdo->exec("DELETE FROM aux_cross_usuarios_grupos WHERE id_usuario IN ($ids) OR id_grupo IN ($ids)");
        $this->pdo->exec('DELETE FROM aux_usuarios WHERE id_usuario = ' . self::USUARIO);
        $this->pdo->exec("DELETE FROM ONLY aux_grupos_y_usuarios WHERE id_usuario IN ($ids)");
    }

    private function regla(int $idUsuario, string $tipo, int $afecta, int $fase, int $on, int $off, bool $propia = true): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO aux_usuarios_perm (id_usuario, dl_propia, id_tipo_activ_txt, fase_ref, afecta_a, perm_on, perm_off)
             VALUES (:u, :p, :t, :f, :a, :on, :off)'
        );
        $stmt->bindValue('u', $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue('p', $propia, PDO::PARAM_BOOL);
        $stmt->bindValue('t', $tipo);
        $stmt->bindValue('f', $fase, PDO::PARAM_INT);
        $stmt->bindValue('a', $afecta, PDO::PARAM_INT);
        $stmt->bindValue('on', $on, PDO::PARAM_INT);
        $stmt->bindValue('off', $off, PDO::PARAM_INT);
        $stmt->execute();
    }

    private function permisos(int $idUsuario = self::USUARIO): PermisosActividades
    {
        /** @var PermisosActividades $p */
        $p = DependencyResolver::make(PermisosActividades::class, ['idUsuario' => $idUsuario]);

        return $p;
    }

    /** Permiso de «datos» (u otro afecta) para una actividad con las fases dadas completadas. */
    private function permiso(PermisosActividades $p, string $afecta, bool $propia, array $fases, string $tipo = '171001'): \src\procesos\domain\PermAccion
    {
        $p->setActividad(42, $tipo, $propia ? ConfigGlobal::mi_delef() : 'dl_ajena_test');
        $p->setFasesCompletadas($fases);

        return $p->getPermisoActual($afecta);
    }

    public function test_sin_reglas_no_hay_permiso(): void
    {
        $p = $this->permisos();

        $this->assertFalse($p->getPermisos(self::D, '171001'));
        $this->assertFalse($this->permiso($p, 'datos', true, [10])->have_perm_activ('ocupado'));
    }

    public function test_carga_las_reglas_del_usuario_separando_dl_propia_y_otras(): void
    {
        $this->regla(self::USUARIO, '17....', self::D, 10, 7, 3, true);
        $this->regla(self::USUARIO, '17....', self::D, 10, 3, 1, false);
        $p = $this->permisos();

        $propia = $this->permiso($p, 'datos', true, [10]);
        $this->assertTrue($propia->have_perm_activ('modificar'));

        $ajena = $this->permiso($p, 'datos', false, [10]);
        $this->assertTrue($ajena->have_perm_activ('ver'));
        $this->assertFalse($ajena->have_perm_activ('modificar'));
    }

    public function test_fase_sin_completar_usa_el_permiso_off(): void
    {
        $this->regla(self::USUARIO, '17....', self::D, 10, 7, 3);
        $p = $this->permisos();

        $perm = $this->permiso($p, 'datos', true, [99]);

        $this->assertTrue($perm->have_perm_activ('ver'));
        $this->assertFalse($perm->have_perm_activ('modificar'));
    }

    public function test_el_usuario_hereda_las_reglas_de_sus_grupos(): void
    {
        $this->regla(self::GRUPO_A, '17....', self::D, 10, 15, 15);
        $p = $this->permisos();

        $this->assertTrue($this->permiso($p, 'datos', true, [10])->have_perm_activ('crear'));
    }

    public function test_las_reglas_de_un_grupo_al_que_no_pertenece_no_se_aplican(): void
    {
        $this->regla(self::GRUPO_AJENO, '17....', self::D, 10, 31, 31);
        $p = $this->permisos();

        $this->assertFalse($this->permiso($p, 'datos', true, [10])->have_perm_activ('ocupado'));
    }

    public function test_la_regla_del_usuario_prevalece_sobre_la_del_grupo(): void
    {
        $this->regla(self::GRUPO_A, '17....', self::D, 10, 31, 31);
        $this->regla(self::USUARIO, '17....', self::D, 10, 3, 3);
        $p = $this->permisos();

        $perm = $this->permiso($p, 'datos', true, [10]);

        $this->assertTrue($perm->have_perm_activ('ver'));
        $this->assertFalse($perm->have_perm_activ('modificar'), 'manda el usuario aunque el grupo diera más');
    }

    public function test_el_usuario_puede_tener_menos_permisos_que_su_grupo_para_el_mismo_afecta(): void
    {
        $this->regla(self::GRUPO_A, '17....', self::D, 10, 15, 15);
        $this->regla(self::USUARIO, '17....', self::D, 10, 0, 0);
        $p = $this->permisos();

        $this->assertFalse($this->permiso($p, 'datos', true, [10])->have_perm_activ('ocupado'));
    }

    public function test_el_grupo_aporta_los_afecta_que_el_usuario_no_define(): void
    {
        $this->regla(self::GRUPO_A, '17....', self::SACD, 10, 15, 15);
        $this->regla(self::USUARIO, '17....', self::D, 10, 3, 3);
        $p = $this->permisos();

        $this->assertTrue($this->permiso($p, 'sacd', true, [10])->have_perm_activ('crear'));
        $this->assertTrue($this->permiso($p, 'datos', true, [10])->have_perm_activ('ver'));
    }

    public function test_entre_grupos_prevalece_el_primero_por_orden_alfabetico(): void
    {
        $this->regla(self::GRUPO_B, '17....', self::D, 10, 31, 31);
        $this->regla(self::GRUPO_A, '17....', self::D, 10, 3, 3);
        $p = $this->permisos();

        $perm = $this->permiso($p, 'datos', true, [10]);

        $this->assertTrue($perm->have_perm_activ('ver'));
        $this->assertFalse($perm->have_perm_activ('modificar'), 'tstg_a gana a tstg_b');
    }

    /**
     * @dataProvider ordenDeInsercionProvider
     */
    public function test_la_clave_canonica_prevalece_sobre_el_alias_truncado(bool $aliasPrimero): void
    {
        $alias = fn() => $this->regla(self::USUARIO, '17', self::D, 10, 3, 3);
        $canonica = fn() => $this->regla(self::USUARIO, '17....', self::D, 10, 15, 15);
        if ($aliasPrimero) {
            $alias();
            $canonica();
        } else {
            $canonica();
            $alias();
        }
        $p = $this->permisos();

        $this->assertTrue($this->permiso($p, 'datos', true, [10])->have_perm_activ('crear'));
    }

    /**
     * @return iterable<string, array{bool}>
     */
    public static function ordenDeInsercionProvider(): iterable
    {
        yield 'alias insertado primero' => [true];
        yield 'canónica insertada primero' => [false];
    }

    public function test_un_alias_truncado_solo_se_aplica_como_clave_canonica(): void
    {
        $this->regla(self::USUARIO, '17', self::D, 10, 7, 7);
        $p = $this->permisos();

        $this->assertTrue($this->permiso($p, 'datos', true, [10])->have_perm_activ('modificar'), "'17' se interpreta como '17....'");
    }

    public function test_la_regla_mas_especifica_gana_tambien_al_cargar_de_bd(): void
    {
        $this->regla(self::USUARIO, '17....', self::D, 10, 3, 3);
        $this->regla(self::USUARIO, '171...', self::D, 20, 31, 31);
        $p = $this->permisos();

        $this->assertTrue($this->permiso($p, 'datos', true, [20])->have_perm_activ('borrar'));
        $this->assertTrue($this->permiso($p, 'datos', true, [10], '172001')->have_perm_activ('ver'), 'otro subtipo: solo 17....');
        $this->assertFalse($this->permiso($p, 'datos', true, [10], '172001')->have_perm_activ('modificar'));
    }

    public function test_la_matriz_cargada_sobrevive_a_la_serializacion_de_sesion(): void
    {
        $this->regla(self::USUARIO, '17....', self::D, 10, 7, 3);
        $p = $this->permisos();

        /** @var PermisosActividades $copia */
        $copia = unserialize(serialize($p));

        $this->assertTrue($this->permiso($copia, 'datos', true, [10])->have_perm_activ('modificar'));
    }
}
