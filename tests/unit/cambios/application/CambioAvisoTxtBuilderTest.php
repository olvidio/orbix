<?php

declare(strict_types=1);

namespace Tests\unit\cambios\application;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\cambios\application\ActividadParaAvisoLookup;
use src\cambios\application\CambioAvisoTxtBuilder;
use src\cambios\application\PersonaNombreParaAvisoInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\StatusId;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\entity\ActividadFase;
use src\shared\domain\value_objects\DateTimeLocal;

final class CambioAvisoTxtBuilderTest extends TestCase
{
    private function createBuilder(
        ActividadAll $actividad,
        PersonaNombreParaAvisoInterface $nombreParaAviso,
    ): CambioAvisoTxtBuilder {
        $allRepository = $this->createMock(ActividadAllRepositoryInterface::class);
        $allRepository->method('findById')->willReturn($actividad);

        $exRepository = $this->createMock(ActividadExRepositoryInterface::class);
        $exRepository->method('findById')->willReturn(null);

        return new CambioAvisoTxtBuilder(
            new ActividadParaAvisoLookup($allRepository, $exRepository),
            $this->createMock(CambioRepositoryInterface::class),
            $nombreParaAviso,
            $this->createMock(TipoTarifaRepositoryInterface::class),
            $this->createMock(RepeticionRepositoryInterface::class),
            $this->createMock(ActividadFaseRepositoryInterface::class),
        );
    }

    public function test_delete_asistente_muestra_nombre_no_id_nom(): void
    {
        $nombreParaAviso = $this->createMock(PersonaNombreParaAvisoInterface::class);
        $nombreParaAviso->expects($this->once())
            ->method('resolve')
            ->with(10012845)
            ->willReturn('García López, Juan');

        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getNom_activ')->willReturn('cv agd Castelldaura');

        $builder = $this->createBuilder($actividad, $nombreParaAviso);

        $cambio = new Cambio();
        $cambio->setId_tipo_cambio(Cambio::TIPO_CMB_DELETE);
        $cambio->setObjeto('Asistente');
        $cambio->setId_activ(10);
        $cambio->setPropiedad('id_nom');
        $cambio->setValor_old('10012845');
        $cambio->setValor_new(null);

        $txt = $builder->build($cambio);

        $this->assertIsString($txt);
        $this->assertStringContainsString('García López, Juan', $txt);
        $this->assertStringNotContainsString('10012845', $txt);
        $this->assertStringContainsString('cv agd Castelldaura', $txt);
    }

    public function test_insert_asistente_muestra_nombre_no_id_nom(): void
    {
        $nombreParaAviso = $this->createMock(PersonaNombreParaAvisoInterface::class);
        $nombreParaAviso->expects($this->once())
            ->method('resolve')
            ->with(55)
            ->willReturn('Pérez, Ana');

        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getNom_activ')->willReturn('cv n prueba');

        $builder = $this->createBuilder($actividad, $nombreParaAviso);

        $cambio = new Cambio();
        $cambio->setId_tipo_cambio(Cambio::TIPO_CMB_INSERT);
        $cambio->setObjeto('AsistenteDl');
        $cambio->setId_activ(3);
        $cambio->setPropiedad('id_nom');
        $cambio->setValor_old(null);
        $cambio->setValor_new('55');

        $txt = $builder->build($cambio);

        $this->assertIsString($txt);
        $this->assertStringContainsString('Pérez, Ana', $txt);
        $this->assertStringNotContainsString('"55"', $txt);
    }

    public function test_insert_sacd_de_paso_resuelve_id_nom_negativo(): void
    {
        $nombreParaAviso = $this->createMock(PersonaNombreParaAvisoInterface::class);
        $nombreParaAviso->expects($this->once())
            ->method('resolve')
            ->with(-10016280)
            ->willReturn('Martínez, Pedro');

        $actividad = $this->createMock(ActividadAll::class);
        $actividad->method('getNom_activ')->willReturn('cv n verano');

        $builder = $this->createBuilder($actividad, $nombreParaAviso);

        $cambio = new Cambio();
        $cambio->setId_tipo_cambio(Cambio::TIPO_CMB_INSERT);
        $cambio->setObjeto('ActividadCargoSacd');
        $cambio->setId_activ(8);
        $cambio->setPropiedad('id_nom');
        $cambio->setValor_old(null);
        $cambio->setValor_new('-10016280');

        $txt = $builder->build($cambio);

        $this->assertIsString($txt);
        $this->assertStringContainsString('Martínez, Pedro', $txt);
        $this->assertStringNotContainsString('-10016280', $txt);
    }

    private mixed $sesionAnterior = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['idioma' => 'es', 'sfsv' => 1];
    }

    protected function tearDown(): void
    {
        if ($this->sesionAnterior === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->sesionAnterior;
        }
        parent::tearDown();
    }

    /**
     * Builder con una actividad de nombre `$nomActiv` (null = no existe) y los repositorios indicados.
     *
     * @param list<ActividadFase> $fases
     * @param array<int|string, string> $tarifas
     * @param array<int|string, string> $repeticiones
     */
    private function builder(
        ?string $nomActiv = 'cv n prueba',
        array $fases = [],
        array $tarifas = [],
        array $repeticiones = [],
        string $nomEliminada = '',
    ): CambioAvisoTxtBuilder {
        $actividad = null;
        if ($nomActiv !== null) {
            $actividad = $this->createStub(ActividadAll::class);
            $actividad->method('getNom_activ')->willReturn($nomActiv);
        }
        $allRepository = $this->createStub(ActividadAllRepositoryInterface::class);
        $allRepository->method('findById')->willReturn($actividad);
        $exRepository = $this->createStub(ActividadExRepositoryInterface::class);
        $exRepository->method('findById')->willReturn(null);
        $cambioRepository = $this->createStub(CambioRepositoryInterface::class);
        $cambioRepository->method('getNomActivEliminada')->willReturn($nomEliminada);
        $tarifaRepository = $this->createStub(TipoTarifaRepositoryInterface::class);
        $tarifaRepository->method('getArrayTipoTarifas')->willReturn($tarifas);
        $repeticionRepository = $this->createStub(RepeticionRepositoryInterface::class);
        $repeticionRepository->method('getArrayRepeticion')->willReturn($repeticiones);
        $faseRepository = $this->createStub(ActividadFaseRepositoryInterface::class);
        $faseRepository->method('matching')->willReturn($fases);

        return new CambioAvisoTxtBuilder(
            new ActividadParaAvisoLookup($allRepository, $exRepository),
            $cambioRepository,
            $this->createStub(PersonaNombreParaAvisoInterface::class),
            $tarifaRepository,
            $repeticionRepository,
            $faseRepository,
        );
    }

    private function cambio(int $tipo, string $objeto, ?string $propiedad, ?string $old, ?string $new): Cambio
    {
        $cambio = new Cambio();
        $cambio->setId_tipo_cambio($tipo);
        $cambio->setObjeto($objeto);
        $cambio->setId_activ(10);
        $cambio->setPropiedad($propiedad);
        $cambio->setValor_old($old);
        $cambio->setValor_new($new);

        return $cambio;
    }

    public function test_sin_actividad_no_hay_aviso(): void
    {
        $this->assertFalse($this->builder(null)->build($this->cambio(Cambio::TIPO_CMB_INSERT, 'Actividad', null, null, null)));
    }

    public function test_actividad_eliminada_usa_el_nombre_guardado_al_eliminarla(): void
    {
        $txt = $this->builder('', nomEliminada: 'cv n borrada')->build($this->cambio(Cambio::TIPO_CMB_DELETE, 'Actividad', null, 'cv n borrada', null));

        $this->assertSame('Actividad: se ha eliminado la actividad "cv n borrada"', $txt);
    }

    public function test_asistente_sin_valores_no_genera_aviso(): void
    {
        $this->assertFalse($this->builder()->build($this->cambio(Cambio::TIPO_CMB_UPDATE, 'AsistenteOut', 'observ', null, '')));
    }

    /**
     * @return iterable<string, array{int, string, string}>
     */
    public static function textosProvider(): iterable
    {
        $i = Cambio::TIPO_CMB_INSERT;
        $u = Cambio::TIPO_CMB_UPDATE;
        $d = Cambio::TIPO_CMB_DELETE;
        yield 'alta actividad' => [$i, 'ActividadDl', 'Actividad: se ha creado la actividad "cv n prueba"'];
        yield 'alta cargo' => [$i, 'ActividadCargo', 'Cl: se ha asignado un cargo a "nuevo" a la actividad "cv n prueba"'];
        yield 'alta sacd' => [$i, 'ActividadCargoSacd', 'Sacd: se ha asignado el sacd "nuevo" a la actividad "cv n prueba"'];
        yield 'alta asistente' => [$i, 'AsistenteEx', 'Asistencia: "nuevo" se ha incorporado a la actividad "cv n prueba"'];
        yield 'alta centro' => [$i, 'CentroEncargado', 'Ctr: se ha asignado el ctr "nuevo" a la actividad "cv n prueba"'];
        yield 'cambio actividad' => [$u, 'ActividadEx', 'Actividad: la actividad "cv n prueba" ha cambiado el campo "observ" de "viejo" a "nuevo"'];
        yield 'cambio cargo' => [$u, 'ActividadCargo', 'Cl: ha cambiado el cargo en la actividad "cv n prueba" el campo "observ" de "viejo" a "nuevo"'];
        yield 'cambio sacd' => [$u, 'ActividadCargoSacd', 'Sacd: ha cambiado el cargo en la actividad "cv n prueba" el campo "observ" de "viejo" a "nuevo"'];
        yield 'cambio asistente' => [$u, 'Asistente', 'Asistente: ha cambiado la asistencia en la actividad "cv n prueba" el campo "observ" de "viejo" a "nuevo"'];
        yield 'cambio centro' => [$u, 'CentroEncargado', 'Ctr: ctr "observ" Ha cambiado a la actividad "cv n prueba"'];
        yield 'baja actividad' => [$d, 'Actividad', 'Actividad: se ha eliminado la actividad "viejo"'];
        yield 'baja cargo' => [$d, 'ActividadCargo', 'Cl: se ha quitado el cargo a "viejo" de la actividad "cv n prueba"'];
        yield 'baja sacd' => [$d, 'ActividadCargoSacd', 'Sacd: se ha quitado al sacd "viejo" de la actividad "cv n prueba"'];
        yield 'baja asistente' => [$d, 'AsistenteDl', 'Asistencia: "viejo" se ha borrado de la actividad "cv n prueba"'];
        yield 'baja centro' => [$d, 'CentroEncargado', 'Ctr: se ha quitado al ctr "viejo" de la actividad "cv n prueba"'];
        yield 'objeto sin texto propio' => [$u, 'Otro', 'cv n prueba; observ; viejo; nuevo'];
    }

    #[DataProvider('textosProvider')]
    public function test_texto_de_cada_tipo_de_cambio_y_objeto(int $tipo, string $objeto, string $esperado): void
    {
        $this->assertSame($esperado, $this->builder()->build($this->cambio($tipo, $objeto, 'observ', 'viejo', 'nuevo')));
    }

    public function test_valores_vacios_salen_como_guion(): void
    {
        $txt = $this->builder()->build($this->cambio(Cambio::TIPO_CMB_UPDATE, 'Actividad', 'observ', null, 'algo'));

        $this->assertSame('Actividad: la actividad "cv n prueba" ha cambiado el campo "observ" de "-" a "algo"', $txt);
    }

    public function test_los_valores_de_la_actividad_se_traducen_a_su_texto(): void
    {
        $status = StatusId::getArrayStatus();
        $nivel = NivelStgrId::getArrayNivelStgr();
        $kNivel = array_key_first($nivel);
        $builder = $this->builder(tarifas: [3 => 'tarifa A', 4 => 'tarifa B'], repeticiones: [1 => 'anual']);
        $txt = fn(string $prop, ?string $old, ?string $new) => $builder->build($this->cambio(Cambio::TIPO_CMB_UPDATE, 'Actividad', $prop, $old, $new));

        $this->assertSame(sprintf('Actividad: la actividad "cv n prueba" ha cambiado el campo "status" de "%s" a "%s"', $status[StatusId::PROYECTO], $status[StatusId::ACTUAL]),
            $txt('status', (string) StatusId::PROYECTO, (string) StatusId::ACTUAL));
        $this->assertSame('Actividad: la actividad "cv n prueba" ha cambiado el campo "id_tarifa" de "tarifa A" a "99"', $txt('id_tarifa', '3', '99'));
        $this->assertSame('Actividad: la actividad "cv n prueba" ha cambiado el campo "id_repeticion" de "-" a "anual"', $txt('id_repeticion', null, '1'));
        $this->assertSame(sprintf('Actividad: la actividad "cv n prueba" ha cambiado el campo "nivel_stgr" de "-" a "%s"', $nivel[$kNivel]), $txt('nivel_stgr', '', (string) $kNivel));
        $this->assertSame(sprintf('Actividad: la actividad "cv n prueba" ha cambiado el campo "f_ini" de "%s" a "%s"',
            (new DateTimeLocal('2026-11-01'))->getFromLocal(), (new DateTimeLocal('2026-11-16'))->getFromLocal()),
            $txt('f_ini', '2026-11-01', '2026-11-16'));
    }

    public function test_fase_marcada_o_desmarcada_con_su_descripcion(): void
    {
        $fase = $this->createStub(ActividadFase::class);
        $fase->method('getDesc_fase')->willReturn('aprobada');
        $builder = $this->builder(fases: [$fase]);

        $this->assertSame('Fase "aprobada" marcada en la actividad "cv n prueba"', $builder->build($this->cambio(Cambio::TIPO_CMB_FASE, 'ActividadProcesoTarea', 'completado', '5', 't')));
        $this->assertSame('Fase "aprobada" desmarcada en la actividad "cv n prueba"', $builder->build($this->cambio(Cambio::TIPO_CMB_FASE, 'ActividadProcesoTarea', 'completado', '5', 'f')));
    }

    public function test_fase_de_una_actividad_eliminada(): void
    {
        $txt = $this->builder('', nomEliminada: 'cv n borrada')->build($this->cambio(Cambio::TIPO_CMB_FASE, 'ActividadProcesoTarea', 'completado', '5', 't'));

        $this->assertSame('Fase cambiada en la actividad "cv n borrada"', $txt);
    }

    /** Una fase que ya no está en el catálogo (se borró después del cambio) no puede cortar la generación de avisos. */
    public function test_fase_que_ya_no_existe_genera_el_aviso_con_su_id(): void
    {
        $txt = $this->builder(fases: [])->build($this->cambio(Cambio::TIPO_CMB_FASE, 'ActividadProcesoTarea', 'completado', '5', 't'));

        $this->assertSame('Fase "5" marcada en la actividad "cv n prueba"', $txt);
    }
}
