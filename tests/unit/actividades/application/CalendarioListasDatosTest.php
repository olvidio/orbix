<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use frontend\shared\web\Lista;
use PHPUnit\Framework\TestCase;
use src\actividades\application\CalendarioListasDatos;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\shared\domain\value_objects\TimeLocal;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;

final class CalendarioListasDatosTest extends TestCase
{
    private mixed $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1];
    }

    protected function tearDown(): void
    {
        if ($this->previousSession === null) {
            unset($_SESSION);
        } else {
            $_SESSION = $this->previousSession;
        }
        parent::tearDown();
    }

    public function test_que_desconocido_devuelve_mensaje(): void
    {
        $out = (new CalendarioListasDatos(
            $this->createMock(CasaDlRepositoryInterface::class),
            $this->createMock(TipoTarifaRepositoryInterface::class),
            $this->createMock(ActividadRepositoryInterface::class),
            $this->createMock(CentroEncargadoRepositoryInterface::class),
            $this->createMock(CasaRepositoryInterface::class),
            $this->createMock(CentroRepositoryInterface::class),
            $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(AsistenteActividadService::class),
        ))->ejecutar(['que' => '__no_existe__']);
        $this->assertArrayHasKey('html', $out);
        $this->assertStringContainsString('opción no definida', $out['html']);
    }

    /**
     * {@see CalendarioListasDatos} usa el mismo patrón que {@see ListaActivTabla}:
     * getH_ini()/getH_fin() devuelven TimeLocal, no string para preg_replace.
     */
    public function test_formato_hora_time_local_sin_segundos(): void
    {
        $hIni = TimeLocal::fromString('09:15:00');
        $hFin = TimeLocal::fromString('18:45:30');

        $this->assertSame('09:15', $hIni->format('H:i'));
        $this->assertSame('18:45', $hFin->format('H:i'));
    }

    /**
     * Las filas de calendario listas son asociativas (sfsv, h_ini, …); sin `field` en
     * cabeceras SlickGrid genera columnas con otro id y las celdas quedan vacías.
     */
    public function test_lista_paginada_acepta_grupo_sin_actividades_como_array_vacio(): void
    {
        $oTabla = new Lista();
        $oTabla->setGrupos([10 => 'Casa sin actividades', 20 => 'Casa con datos']);
        $oTabla->setCabeceras([['name' => 'Tipo', 'field' => 'tipo_activ']]);
        $oTabla->setDatos([
            10 => [],
            20 => [1 => ['tipo_activ' => 'reunión']],
        ]);

        $html = $oTabla->listaPaginada();

        $this->assertStringContainsString('Casa sin actividades', $html);
        $this->assertStringContainsString('reunión', $html);
    }

    public function test_lista_con_cabeceras_field_muestra_valores_asociativos(): void
    {
        $oTabla = new Lista();
        $oTabla->setGrupos([1 => 'Casa prueba']);
        $oTabla->setCabeceras([
            ['name' => 'Hora inicio', 'field' => 'h_ini'],
            ['name' => 'Tipo', 'field' => 'tipo_activ'],
        ]);
        $oTabla->setDatos([
            1 => [
                1 => [
                    'h_ini' => '09:15',
                    'tipo_activ' => 'n crt reunión',
                ],
            ],
        ]);

        $html = $oTabla->listaPaginada();

        $this->assertStringContainsString('09:15', $html);
        $this->assertStringContainsString('n crt reunión', $html);
    }
}
