<?php

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\TestCase;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\Select_asistentes_a_una_actividad;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\services\TelecoPersonaService;

/**
 * La dl organizadora ve a quien otra dl le ha pedido (propietario «org>otra»), pero solo cuenta
 * (num y resumen de plazas) cuando la plaza está asignada. Las denegadas no se muestran.
 */
final class SelectAsistentesAUnaActividadPlazasPedidasTest extends TestCase
{
    private const ID_ACTIV = 100125339;
    private const ID_NOM = 100517180;

    /** @var mixed */
    private $configAnterior;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configAnterior = $_SESSION['config'] ?? null;
        $config = is_array($this->configAnterior) ? $this->configAnterior : [];
        $config['a_apps']['actividadplazas'] = 998;
        $config['app_installed'] = [998];
        $_SESSION['config'] = $config;
    }

    protected function tearDown(): void
    {
        $_SESSION['config'] = $this->configAnterior;
        parent::tearDown();
    }

    private function sut(int $plaza): Select_asistentes_a_una_actividad
    {
        $asistente = new Asistente();
        $asistente->setId_activ(self::ID_ACTIV);
        $asistente->setId_nom(self::ID_NOM);
        $asistente->setPropietario('dlb>dlp');
        $asistente->setDl_responsable('dlp');
        $asistente->setPlaza($plaza);

        $asistentes = $this->createMock(AsistenteRepositoryInterface::class);
        $asistentes->method('matching')->willReturn([$asistente]);

        $finder = $this->createMock(PersonaFinderService::class);
        $finder->method('findPersonaParaListado')->willReturn($this->createMock(PersonaDl::class));

        $sut = new Select_asistentes_a_una_actividad(
            $this->createMock(ActividadAllRepositoryInterface::class),
            $this->createMock(ResumenPlazasService::class),
            $asistentes,
            $this->createMock(ActividadCargoRepositoryInterface::class),
            $this->createMock(CargoRepositoryInterface::class),
            $finder,
            $this->createMock(TelecoPersonaService::class),
        );
        // Soy dlb, la organizadora; el asistente es de dlp.
        $this->poner($sut, 'mi_dele', 'dlb');
        $this->poner($sut, 'dl_org', 'dlb');
        $this->poner($sut, 'id_pau', self::ID_ACTIV);

        return $sut;
    }

    private function poner(object $o, string $prop, mixed $valor): void
    {
        $r = new \ReflectionProperty($o, $prop);
        $r->setValue($o, $valor);
    }

    private function leer(object $o, string $prop): mixed
    {
        return (new \ReflectionProperty($o, $prop))->getValue($o);
    }

    public function test_plaza_pedida_se_muestra_pero_no_cuenta(): void
    {
        $sut = $this->sut(PlazaId::PEDIDA);
        $sut->getAsistentes();

        $this->assertCount(1, $this->leer($sut, 'a_asistentes'));
        $this->assertSame(0, $this->leer($sut, 'num'));
        $this->assertSame([], $this->leer($sut, 'a_plazas_conseguidas'));
        $this->assertSame([], $this->leer($sut, 'a_plazas_resumen'));
    }

    public function test_plaza_en_espera_se_muestra_pero_no_cuenta(): void
    {
        $sut = $this->sut(PlazaId::EN_ESPERA);
        $sut->getAsistentes();

        $this->assertCount(1, $this->leer($sut, 'a_asistentes'));
        $this->assertSame(0, $this->leer($sut, 'num'));
        $this->assertSame([], $this->leer($sut, 'a_plazas_conseguidas'));
    }

    public function test_plaza_asignada_se_muestra_y_cuenta(): void
    {
        $sut = $this->sut(PlazaId::ASIGNADA);
        $sut->getAsistentes();

        $this->assertCount(1, $this->leer($sut, 'a_asistentes'));
        $this->assertSame(1, $this->leer($sut, 'num'));
        $this->assertNotSame([], $this->leer($sut, 'a_plazas_conseguidas'));
        $this->assertNotSame([], $this->leer($sut, 'a_plazas_resumen'));
    }

    public function test_plaza_denegada_no_se_muestra(): void
    {
        $sut = $this->sut(PlazaId::DENEGADA);
        $sut->getAsistentes();

        $this->assertCount(0, $this->leer($sut, 'a_asistentes'));
    }
}
