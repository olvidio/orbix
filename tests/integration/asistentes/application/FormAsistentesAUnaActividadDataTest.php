<?php

namespace Tests\integration\asistentes\application;

use src\asistentes\application\FormAsistentesAUnaActividadData;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\value_objects\AsistentePk;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\shared\config\ConfigGlobal;
use Tests\factories\asistentes\AsistenteFactory;
use Tests\myTest;

/**
 * Regla «los datos de asistencia los modifica el propietario de la plaza» del formulario de
 * asistentes de una actividad: con `actividadplazas` instalada, un propietario `origen>destino`
 * cuyo destino no es mi delegación impide la edición. Es una situación prevista: el caso de uso
 * responde con `aviso_txt` (respuesta válida), no con `error`, que se reserva para fallos reales.
 */
class FormAsistentesAUnaActividadDataTest extends myTest
{
    private const ACT = 998830001;

    private AsistenteDlRepositoryInterface $repoDl;
    private FormAsistentesAUnaActividadData $caso;
    private int $idNom;
    /** @var mixed */
    private $configAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $c = $GLOBALS['container'];
        $this->repoDl = $c->get(AsistenteDlRepositoryInterface::class);
        $this->caso = $c->get(FormAsistentesAUnaActividadData::class);

        $personas = $c->get(PersonaNRepositoryInterface::class)->getArrayPersonas();
        if ($personas === []) {
            $this->markTestSkipped('No hay ninguna persona n en la BD de pruebas');
        }
        $this->idNom = (int) array_key_first($personas);

        $this->configAnterior = $_SESSION['config'] ?? null;
        $this->limpiar();
    }

    public function tearDown(): void
    {
        $this->limpiar();
        $_SESSION['config'] = $this->configAnterior;
        parent::tearDown();
    }

    private function limpiar(): void
    {
        $a = $this->repoDl->findById(self::ACT, $this->idNom);
        if ($a !== null) {
            $this->repoDl->Eliminar($a, false);
        }
    }

    private function plazasInstalada(bool $si): void
    {
        $config = is_array($this->configAnterior) ? $this->configAnterior : [];
        $config['a_apps']['actividadplazas'] = 998;
        $config['app_installed'] = $si ? [998] : [];
        $_SESSION['config'] = $config;
    }

    private function sembrar(string $propietario): void
    {
        $a = (new AsistenteFactory())->createSimple(new AsistentePk(self::ACT, $this->idNom));
        $a->setPropietario($propietario);
        $this->assertTrue($this->repoDl->Guardar($a, false));
    }

    /** Aviso de la regla (respuesta válida `aviso_txt`), o null si no se aplica. */
    private function avisoDeLaRegla(): ?string
    {
        $r = $this->caso->build(['id_activ' => self::ACT, 'id_nom' => $this->idNom]);
        $this->assertArrayNotHasKey('error', $r, 'la regla de propietario no es un error: error=' . ($r['error'] ?? ''));
        return isset($r['aviso_txt']) ? (string) $r['aviso_txt'] : null;
    }

    public function test_avisa_si_el_propietario_de_la_plaza_es_otra_dl(): void
    {
        $this->plazasInstalada(true);
        $this->sembrar('dlmE>dlmE');
        $this->assertNotSame('dlmE', ConfigGlobal::mi_delef(), 'el test supone que mi dl no es dlmE');

        $aviso = $this->avisoDeLaRegla();
        $this->assertNotNull($aviso);
        $this->assertStringContainsString('propietario de la plaza', $aviso);
        $this->assertStringContainsString('dlmE', $aviso);
    }

    public function test_no_avisa_si_el_propietario_soy_yo(): void
    {
        $this->plazasInstalada(true);
        $this->sembrar('dlmE>' . ConfigGlobal::mi_delef());
        $this->assertNull($this->avisoDeLaRegla());
    }

    public function test_no_avisa_sin_propietario(): void
    {
        $this->plazasInstalada(true);
        $this->sembrar('');
        $this->assertNull($this->avisoDeLaRegla());
    }

    public function test_no_avisa_si_actividadplazas_no_esta_instalada(): void
    {
        $this->plazasInstalada(false);
        $this->sembrar('dlmE>dlmE');
        $this->assertNull($this->avisoDeLaRegla());
    }

    public function test_asistente_inexistente_devuelve_error(): void
    {
        $this->plazasInstalada(true);
        $r = $this->caso->build(['id_activ' => self::ACT, 'id_nom' => $this->idNom]);
        $this->assertSame('No se encontró el asistente para esta actividad.', $r['error'] ?? null);
    }
}
