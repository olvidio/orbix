<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\PlazaPropietarioAsignacion;
use src\asistentes\domain\entity\Asistente;
use src\personas\application\services\PersonaFinderService;
use src\personas\domain\entity\PersonaDl;
use src\personas\domain\entity\PersonaEx;

/**
 * Al dar plaza a un asistente, de qué cupo (propietario «dl_org>dl») sale: se respeta el elegido si tiene sitio o
 * se toma el primero libre; las personas de paso cuentan en el cupo de su dl.
 */
final class PlazaPropietarioAsignacionTest extends TestCase
{
    private ResumenPlazasService&MockObject $resumen;
    private PersonaFinderService&MockObject $finder;
    /** @var array<string, mixed> */
    private array $sesionAnterior = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION ?? [];
        $this->instalarPlazas(true);
        $this->resumen = $this->createMock(ResumenPlazasService::class);
        $this->finder = $this->createMock(PersonaFinderService::class);
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->sesionAnterior;
        parent::tearDown();
    }

    private function instalarPlazas(bool $instalada): void
    {
        $_SESSION['config'] = ['a_apps' => ['actividadplazas' => 931], 'app_installed' => $instalada ? [931] : []];
    }

    private function sut(): PlazaPropietarioAsignacion
    {
        return new PlazaPropietarioAsignacion($this->resumen, $this->finder);
    }

    private function asistente(?string $propietario = null, int $idNom = 10011): Asistente
    {
        $a = new Asistente();
        $a->setId_activ(3001);
        $a->setId_nom($idNom);
        $a->setPropietarioVo($propietario);

        return $a;
    }

    public function test_sin_el_modulo_de_plazas_no_hace_nada(): void
    {
        $this->instalarPlazas(false);
        $this->resumen->expects($this->never())->method('setId_activ');

        $this->assertSame('', $this->sut()->asegurar($this->asistente(), PlazaId::PEDIDA, PlazaId::ASIGNADA));
    }

    #[DataProvider('sinComprobacionProvider')]
    public function test_no_comprueba_cupo_si_la_plaza_nueva_no_ocupa_o_si_ya_la_ocupaba(int $actual, int $nueva): void
    {
        $this->resumen->expects($this->never())->method('setId_activ');
        $a = $this->asistente();

        $this->assertSame('', $this->sut()->asegurar($a, $actual, $nueva));
        $this->assertNull($a->getPropietarioVo());
    }

    /**
     * @return iterable<string, array{int, int}>
     */
    public static function sinComprobacionProvider(): iterable
    {
        yield 'pasa a pedida' => [PlazaId::PEDIDA, PlazaId::PEDIDA];
        yield 'pasa a en espera' => [PlazaId::PEDIDA, PlazaId::EN_ESPERA];
        yield 'pasa a denegada' => [PlazaId::ASIGNADA, PlazaId::DENEGADA];
        yield 'ya estaba asignada y pasa a confirmada' => [PlazaId::ASIGNADA, PlazaId::CONFIRMADA];
        yield 'ya estaba confirmada' => [PlazaId::CONFIRMADA, PlazaId::ASIGNADA];
    }

    public function test_sin_propietario_toma_el_primer_cupo_libre_de_la_actividad(): void
    {
        $this->resumen->expects($this->once())->method('setId_activ')->with(3001);
        $this->resumen->expects($this->once())->method('getPrimeraPropiedadLibre')->with(false)->willReturn('dlb>dl');
        $a = $this->asistente();

        $this->assertSame('', $this->sut()->asegurar($a, PlazaId::PEDIDA, PlazaId::ASIGNADA));
        $this->assertSame('dlb>dl', $a->getPropietarioVo()?->value());
    }

    public function test_el_propietario_xxx_cuenta_como_sin_propietario(): void
    {
        $this->resumen->method('getPrimeraPropiedadLibre')->willReturn('dlb>dl');
        $this->resumen->expects($this->never())->method('esPropiedadClaveDisponible');
        $a = $this->asistente('xxx');

        $this->sut()->asegurar($a, PlazaId::EN_ESPERA, PlazaId::CONFIRMADA);

        $this->assertSame('dlb>dl', $a->getPropietarioVo()?->value());
    }

    public function test_sin_cupo_libre_avisa_y_no_pone_propietario(): void
    {
        $this->resumen->method('getPrimeraPropiedadLibre')->willReturn(null);
        $a = $this->asistente();

        $this->assertNotSame('', $this->sut()->asegurar($a, PlazaId::PEDIDA, PlazaId::ASIGNADA));
        $this->assertNull($a->getPropietarioVo());
    }

    public function test_con_propietario_elegido_se_respeta_si_tiene_sitio(): void
    {
        $this->resumen->expects($this->once())->method('esPropiedadClaveDisponible')->with('dlp>dl', false)->willReturn(true);
        $this->resumen->expects($this->never())->method('getPrimeraPropiedadLibre');
        $a = $this->asistente('dlp>dl');

        $this->assertSame('', $this->sut()->asegurar($a, PlazaId::PEDIDA, PlazaId::ASIGNADA));
        $this->assertSame('dlp>dl', $a->getPropietarioVo()?->value());
    }

    public function test_con_propietario_elegido_sin_sitio_avisa_salvo_que_se_permita(): void
    {
        $this->resumen->method('esPropiedadClaveDisponible')->willReturn(false);

        $this->assertNotSame('', $this->sut()->asegurar($this->asistente('dlp>dl'), PlazaId::PEDIDA, PlazaId::ASIGNADA));
        $this->assertSame('', $this->sut()->asegurar($this->asistente('dlp>dl'), PlazaId::PEDIDA, PlazaId::ASIGNADA, true));
    }

    /**
     * Hallazgo: «permitir sin plaza libre» solo vale con un propietario ya elegido; sin propietario y sin cupo
     * libre se avisa igualmente.
     */
    public function test_permitir_sin_plaza_libre_no_sirve_si_no_hay_propietario(): void
    {
        $this->resumen->method('getPrimeraPropiedadLibre')->willReturn(null);

        $this->assertNotSame('', $this->sut()->asegurar($this->asistente(), PlazaId::PEDIDA, PlazaId::ASIGNADA, true));
    }

    public function test_una_persona_de_paso_ocupa_el_cupo_de_su_dl(): void
    {
        $ex = new PersonaEx();
        $ex->setDl('dlz');
        $this->finder->method('findPersonaEnGlobalODePaso')->with(-1005)->willReturn($ex);
        $this->resumen->expects($this->once())->method('getPrimeraPropiedadLibre')->with('dlz')->willReturn('dlb>dlz');

        $this->sut()->asegurar($this->asistente(null, -1005), PlazaId::PEDIDA, PlazaId::ASIGNADA);
    }

    #[DataProvider('sinDlDePasoProvider')]
    public function test_sin_dl_de_paso_se_consulta_el_cupo_general(?object $persona): void
    {
        $this->finder->method('findPersonaEnGlobalODePaso')->willReturn($persona);
        $this->resumen->expects($this->once())->method('esPropiedadClaveDisponible')->with('dlb>dl', false)->willReturn(true);

        $this->sut()->asegurar($this->asistente('dlb>dl'), PlazaId::PEDIDA, PlazaId::ASIGNADA);
    }

    /**
     * @return iterable<string, array{?object}>
     */
    public static function sinDlDePasoProvider(): iterable
    {
        yield 'persona no encontrada' => [null];
        yield 'persona de la dl' => [new PersonaDl()];
        yield 'persona de paso sin dl' => [new PersonaEx()];
    }
}
