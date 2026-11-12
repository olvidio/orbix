<?php

declare(strict_types=1);

namespace Tests\integration\dossiers\application;

use src\dossiers\application\PermDossier;
use src\permisos\domain\XPermisos;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use Tests\myTest;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Permisos de los dossiers (caracterización):
 * - permiso(): 1 sin permiso, 2 lectura, 3 escritura según los bits de menú; con
 *   «depende» la escritura sobre una persona exige además la oficina de su tipo;
 * - perm_activ_pers(): a qué tipos de actividad puede apuntar cada oficina a un
 *   tipo de persona (se suman las oficinas);
 * - perm_pers_activ(): qué tipos de persona puede añadir cada oficina a una
 *   actividad; fuera de la dl propia, nunca personas de paso.
 * Integración porque TiposActividades lee los tipos de la BD.
 */
final class PermDossierTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private mixed $sesion = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesion = $_SESSION;
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        $_SESSION = $this->sesion;
        parent::tearDown();
    }

    private function oficinas(string ...$oficinas): void
    {
        $perm = $this->createMock(XPermisos::class);
        $perm->method('have_perm_oficina')->willReturnCallback(fn (string $o): bool => in_array($o, $oficinas, true));
        $_SESSION['oPerm'] = $perm;
    }

    private function persona(?string $idTabla): void
    {
        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaEnGlobalODePaso')->willReturnCallback(static function () use ($idTabla): ?PersonaDl {
            if ($idTabla === null) {
                return null;
            }
            $p = new PersonaDl();
            $p->setId_nom(100555);
            $p->setId_tabla($idTabla);
            return $p;
        });
        $this->sustituirServicio(PersonaFinderService::class, $finder);
    }

    public function test_permiso_segun_los_bits_de_menu(): void
    {
        $_SESSION['iPermMenus'] = 0b0110; // bits 2 y 4
        $this->assertSame(1, PermDossier::permiso(1, 8, 'f', 'a', 1), 'ningún bit');
        $this->assertSame(2, PermDossier::permiso(2, 8, 'f', 'a', 1), 'lectura');
        $this->assertSame(3, PermDossier::permiso(2, 4, 'f', 'a', 1), 'lectura y escritura');
        $this->assertSame(3, PermDossier::permiso(0, 4, 'f', 'a', 1));
        $_SESSION['iPermMenus'] = 'x';
        $this->assertSame(1, PermDossier::permiso(2, 4, 'f', 'a', 1), 'sin bits en la sesión');
    }

    public function test_escritura_sobre_una_persona_exige_la_oficina_de_su_tipo(): void
    {
        $_SESSION['iPermMenus'] = 4;
        $casos = ['n' => 'sm', 'x' => 'nax', 'a' => 'agd', 's' => 'sg', 'sssc' => 'des', 'pn' => 'sm', 'px' => 'nax', 'pa' => 'agd', 'psssc' => 'des'];
        foreach ($casos as $idTabla => $oficina) {
            $this->persona($idTabla);
            $this->oficinas('otra');
            $this->assertSame(2, PermDossier::permiso(0, 4, 't', 'p', 100555), "$idTabla sin $oficina: solo lectura");
            $this->oficinas($oficina);
            $this->assertSame(3, PermDossier::permiso(0, 4, 't', 'p', 100555), "$idTabla con $oficina");
        }
        $this->persona(null);
        $this->assertSame(2, PermDossier::permiso(0, 4, 't', 'p', 100555), 'persona no encontrada');
        $this->persona('n');
        $this->oficinas();
        $this->assertSame(3, PermDossier::permiso(0, 4, 'f', 'p', 100555), 'sin «depende» no mira la oficina');
        $this->assertSame(3, PermDossier::permiso(0, 4, 't', 'a', 100555), 'solo para personas');
    }

    /** @param array<int|string, array<string, mixed>> $m @return list<string> */
    private static function conPermiso(array $m): array
    {
        $r = [];
        foreach ($m as $k => $v) {
            if (!empty($v['perm'])) {
                $r[] = (string) $k;
            }
        }
        sort($r);
        return $r;
    }

    public function test_tipos_de_actividad_para_un_numerario_segun_la_oficina(): void
    {
        $this->oficinas();
        $this->assertSame([], (new PermDossier())->perm_activ_pers('n'), 'sin oficina, nada');

        $this->oficinas('sm');
        $sm = (new PermDossier())->perm_activ_pers('n');
        $this->assertNotSame([], $sm);
        $this->assertContains('111', self::conPermiso($sm), 'crt n');
        $this->assertContains('112', self::conPermiso($sm), 'ca n');

        $this->oficinas('est');
        $est = self::conPermiso((new PermDossier())->perm_activ_pers('n'));
        $this->assertContains('112', $est, 'est: ca n');
        $this->assertNotContains('111', $est, 'est: crt n no');

        $this->oficinas('est', 'sm');
        $ambas = self::conPermiso((new PermDossier())->perm_activ_pers('n'));
        $this->assertSame([], array_diff(self::conPermiso($sm), $ambas), 'se suman las oficinas');
        $this->assertSame([], array_diff($est, $ambas));
    }

    public function test_tipos_de_actividad_solo_los_que_existen(): void
    {
        $this->oficinas('des');
        foreach (array_keys((new PermDossier())->perm_activ_pers('sssc')) as $tipo) {
            $this->assertMatchesRegularExpression('/^\d{3}$/', (string) $tipo);
        }
    }

    public function test_tipos_de_persona_para_una_actividad_de_n(): void
    {
        $this->oficinas();
        $nada = (new PermDossier())->perm_pers_activ(112000);
        $this->assertSame([], self::conPermiso($nada), 'sin oficina, nada');

        $this->oficinas('sm');
        $this->assertSame(['n', 'pn'], self::conPermiso((new PermDossier())->perm_pers_activ(112000)));
        $fuera = (new PermDossier())->perm_pers_activ(112000, false);
        $this->assertSame(['n'], self::conPermiso($fuera), 'fuera de la dl propia, sin personas de paso');
        $this->assertSame('n de paso', $fuera['pn']['nom'], 'conserva la entrada');
    }

    public function test_tipos_de_persona_se_suman_entre_oficinas(): void
    {
        $this->oficinas('sm', 'des');
        $ambas = self::conPermiso((new PermDossier())->perm_pers_activ(112000));
        $this->oficinas('sm');
        $sm = self::conPermiso((new PermDossier())->perm_pers_activ(112000));
        $this->assertSame([], array_diff($sm, $ambas));
    }
}
