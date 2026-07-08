<?php

declare(strict_types=1);

namespace Tests\integration\cambios\application;

use src\actividades\application\ActividadNueva;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\value_objects\ActividadTipoId;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\StatusId;
use src\cambios\application\AvisosGenerarTabla;
use src\cambios\domain\contracts\CambioAnotadoRepositoryInterface;
use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use src\cambios\domain\entity\CambioAnotado;
use src\cambios\domain\entity\CambioUsuario;
use src\cambios\domain\entity\CambioUsuarioObjetoPref;
use src\cambios\domain\entity\CambioUsuarioPropiedadPref;
use src\cambios\domain\value_objects\AvisoTipoId;
use src\cambios\domain\value_objects\ObjetoNombre;
use src\cambios\domain\value_objects\OperadorPref;
use src\cambios\domain\value_objects\PropiedadNombre;
use src\cambios\domain\value_objects\TipoCambioId;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use src\ubis\domain\value_objects\DelegacionCode;
use Tests\myTest;

/**
 * Integración del flujo: cambio en av_cambios_dl → AvisosGenerarTabla → av_cambios_usuario.
 *
 * Usa actividad real creada en BD, preferencias de objeto/propiedad y sin módulo procesos
 * (fases vacías en el cambio → comparación por id_status / id_fase_ref).
 */
final class AvisosGenerarTablaIntegracionTest extends myTest
{
    private const APP_CAMBIOS_ID = 99002;

    /** @var array{a_apps: mixed, app_installed: mixed} */
    private array $configAppsBackup = [];

    /** @var list<CambioUsuario> */
    private array $avisosCreados = [];

    /** @var list<CambioUsuarioObjetoPref> */
    private array $objetoPrefsCreados = [];

    /** @var list<CambioUsuarioPropiedadPref> */
    private array $propiedadPrefsCreados = [];

    private ?Cambio $cambioCreado = null;

    private int $idActiv = 0;

    public function setUp(): void
    {
        if (!is_string($_SESSION['session_auth']['esquema'] ?? null)
            || $_SESSION['session_auth']['esquema'] === ''
        ) {
            unset($_SESSION['session_auth']);
        }

        parent::setUp();
        $this->configAppsBackup = [
            'a_apps' => $_SESSION['config']['a_apps'] ?? [],
            'app_installed' => $_SESSION['config']['app_installed'] ?? [],
        ];
        $this->avisosCreados = [];
        $this->objetoPrefsCreados = [];
        $this->propiedadPrefsCreados = [];
        $this->cambioCreado = null;
        $this->idActiv = 0;
    }

    public function tearDown(): void
    {
        $this->limpiarDatosDePrueba();
        $_SESSION['config']['a_apps'] = $this->configAppsBackup['a_apps'];
        $_SESSION['config']['app_installed'] = $this->configAppsBackup['app_installed'];
        parent::tearDown();
    }

    public function test_generar_tabla_apunta_sin_propiedades_concretas(): void
    {
        $this->prepararEntornoCambiosSinProcesos();
        $contexto = $this->crearActividadYCambio([
            'propiedad' => 'nom_activ',
            'valor_old' => 'nombre anterior',
            'valor_new' => 'nombre nuevo',
        ], 'ActividadDl');
        $this->crearPreferenciaObjeto($contexto, 'Actividad');

        $resultado = $this->ejecutarGenerarTabla();

        $this->assertFalse($resultado['bucle_infinito']);
        $avisos = $this->buscarAvisosUsuario($contexto);
        $this->assertCount(1, $avisos);
        $this->assertSame(1, $avisos[0]->getAviso_tipo());
        $this->assertCambioAnotado($contexto);
    }

    public function test_generar_tabla_apunta_con_filtro_propiedad_fecha(): void
    {
        $this->prepararEntornoCambiosSinProcesos();
        $contexto = $this->crearActividadYCambio([
            'propiedad' => 'f_ini',
            'valor_old' => '2026-01-01',
            'valor_new' => '2026-07-07',
        ], 'ActividadDl');
        $idObjetoPref = $this->crearPreferenciaObjeto($contexto, 'Actividad', sinPropiedades: false);
        $this->crearPreferenciaPropiedadFecha($idObjetoPref, '2026-01-01');

        $resultado = $this->ejecutarGenerarTabla();

        $this->assertFalse($resultado['bucle_infinito']);
        $avisos = $this->buscarAvisosUsuario($contexto);
        $this->assertCount(1, $avisos);
        $this->assertCambioAnotado($contexto);
    }

    public function test_generar_tabla_no_apunta_si_filtro_fecha_no_coincide(): void
    {
        $this->prepararEntornoCambiosSinProcesos();
        $contexto = $this->crearActividadYCambio([
            'propiedad' => 'f_ini',
            'valor_old' => '2026-01-01',
            'valor_new' => '2026-07-07',
        ], 'ActividadDl');
        $idObjetoPref = $this->crearPreferenciaObjeto($contexto, 'Actividad', sinPropiedades: false);
        $this->crearPreferenciaPropiedadFecha($idObjetoPref, '1999-12-31');

        $resultado = $this->ejecutarGenerarTabla();

        $this->assertFalse($resultado['bucle_infinito']);
        $this->assertSame([], $this->buscarAvisosUsuario($contexto));
        $this->assertCambioAnotado($contexto);
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: string, 4: string}>
     */
    public static function objetosAvisoProvider(): array
    {
        return [
            'Actividad' => ['Actividad', 'ActividadDl', 'nom_activ', 'nombre anterior', 'nombre nuevo'],
            'Asistente' => ['Asistente', 'Asistente', 'id_nom', '10011', '10012'],
            'ActividadCargoSacd' => ['ActividadCargoSacd', 'ActividadCargoSacd', 'id_nom', '20011', '20012'],
            'ActividadCargoNoSacd' => ['ActividadCargoNoSacd', 'ActividadCargoNoSacd', 'id_nom', '30011', '30012'],
        ];
    }

    /**
     * @dataProvider objetosAvisoProvider
     */
    public function test_generar_tabla_apunta_por_tipo_objeto(
        string $objetoPref,
        string $objetoCambio,
        string $propiedad,
        string $valorOld,
        string $valorNew,
    ): void {
        $this->prepararEntornoCambiosSinProcesos();
        $contexto = $this->crearActividadYCambio([
            'propiedad' => $propiedad,
            'valor_old' => $valorOld,
            'valor_new' => $valorNew,
        ], $objetoCambio);
        $this->crearPreferenciaObjeto($contexto, $objetoPref);

        $resultado = $this->ejecutarGenerarTabla();

        $this->assertFalse($resultado['bucle_infinito']);
        $avisos = $this->buscarAvisosUsuario($contexto);
        $this->assertCount(1, $avisos, "Debería apuntar aviso para objeto $objetoPref");
        $this->assertCambioAnotado($contexto);
    }

    private function prepararEntornoCambiosSinProcesos(): void
    {
        $this->aplicarAppInstalada('cambios', true, self::APP_CAMBIOS_ID);
        $this->aplicarAppInstalada('procesos', false, 99003);
        $this->assertTrue(ConfigGlobal::is_app_installed('cambios'));
        $this->assertFalse(ConfigGlobal::is_app_installed('procesos'));
        $this->anotarColaPendienteAlInicio();
    }

    /**
     * @param array{propiedad: string, valor_old: string, valor_new: string} $cambioDatos
     * @return array{
     *     id_activ: int,
     *     id_tipo_activ: int,
     *     dl_org: string,
     *     id_item_cambio: int,
     *     id_schema: int,
     * }
     */
    private function crearActividadYCambio(
        array $cambioDatos,
        string $objetoCambio,
    ): array {
        $idTipo = 271000;
        $dlOrg = ConfigGlobal::mi_delef(substr((string) $idTipo, 0, 1));
        $nom = 'avisos_it_' . uniqid('', true);

        $this->idActiv = (int) DependencyResolver::get(ActividadNueva::class)->actividadNueva([
            'dl_org' => $dlOrg,
            'publicado' => false,
            'id_tipo_activ' => $idTipo,
            'nom_activ' => $nom,
            'f_ini' => '1/6/2099',
            'f_fin' => '2/6/2099',
            'status' => StatusId::PROYECTO,
            'id_ubi' => 0,
            'lugar_esp' => '',
            'desc_activ' => '',
            'precio' => null,
            'num_asistentes' => null,
            'observ' => '',
            'nivel_stgr' => NivelStgrId::N,
            'id_repeticion' => 1,
            'observ_material' => '',
            'tarifa' => null,
            'h_ini' => '',
            'h_fin' => '',
            'plazas' => null,
        ]);
        $this->assertGreaterThan(0, $this->idActiv);

        /** @var CambioDlRepositoryInterface $dlRepository */
        $dlRepository = $GLOBALS['container']->get(CambioDlRepositoryInterface::class);
        $idItemCambio = (int) $dlRepository->getNewId();
        $this->assertGreaterThan(0, $idItemCambio);

        $cambio = new Cambio();
        $cambio->setId_item_cambio($idItemCambio);
        $cambio->setTipoCambioVo(new TipoCambioId(Cambio::TIPO_CMB_UPDATE));
        $cambio->setId_activ($this->idActiv);
        $cambio->setIdTipoActivVo(new ActividadTipoId($idTipo));
        $cambio->setJson_fases_sv([]);
        $cambio->setJson_fases_sf([]);
        $cambio->setIdStatusVo(new StatusId(StatusId::PROYECTO));
        $cambio->setDlOrgVo(new DelegacionCode($dlOrg));
        $cambio->setObjetoVo(new ObjetoNombre($objetoCambio));
        $cambio->setPropiedadVo(new PropiedadNombre($cambioDatos['propiedad']));
        $cambio->setValor_old($cambioDatos['valor_old']);
        $cambio->setValor_new($cambioDatos['valor_new']);
        $cambio->setQuien_cambia(ConfigGlobal::mi_id_usuario());
        $cambio->setSfsv_quien_cambia(ConfigGlobal::mi_sfsv());
        $cambio->setTimestamp_cambio(new DateTimeLocal('now'));

        $this->assertTrue($dlRepository->Guardar($cambio));

        $persistido = $dlRepository->findById($idItemCambio);
        $this->assertNotNull($persistido);
        $this->cambioCreado = $persistido;

        return [
            'id_activ' => $this->idActiv,
            'id_tipo_activ' => $idTipo,
            'dl_org' => $dlOrg,
            'id_item_cambio' => $idItemCambio,
            'id_schema' => $persistido->getId_schema(),
        ];
    }

    /**
     * @param array{
     *     id_activ: int,
     *     id_tipo_activ: int,
     *     dl_org: string,
     *     id_item_cambio: int,
     *     id_schema: int,
     * } $contexto
     */
    private function crearPreferenciaObjeto(
        array $contexto,
        string $objetoPref,
        bool $sinPropiedades = true,
    ): int {
        /** @var CambioUsuarioObjetoPrefRepositoryInterface $repository */
        $repository = $GLOBALS['container']->get(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $idObjetoPref = (int) $repository->getNewId();
        $this->assertGreaterThan(0, $idObjetoPref);

        $pref = new CambioUsuarioObjetoPref();
        $pref->setId_item_usuario_objeto($idObjetoPref);
        $pref->setId_usuario(ConfigGlobal::mi_id_usuario());
        $pref->setDlOrgVo(new DelegacionCode($contexto['dl_org']));
        $pref->setId_tipo_activ_txt((string) $contexto['id_tipo_activ']);
        $pref->setId_fase_ref(StatusId::PROYECTO);
        $pref->setAviso_on(true);
        $pref->setAviso_off(false);
        $pref->setAviso_outdate(false);
        $pref->setObjetoVo(new ObjetoNombre($objetoPref));
        $pref->setAvisoTipoVo(new AvisoTipoId(1));

        $this->assertTrue($repository->Guardar($pref));
        $this->objetoPrefsCreados[] = $pref;

        return $idObjetoPref;
    }

    private function crearPreferenciaPropiedadFecha(int $idObjetoPref, string $valorFiltro): void
    {
        /** @var CambioUsuarioPropiedadPrefRepositoryInterface $repository */
        $repository = $GLOBALS['container']->get(CambioUsuarioPropiedadPrefRepositoryInterface::class);
        $idPropPref = (int) $repository->getNewId();
        $this->assertGreaterThan(0, $idPropPref);

        $pref = new CambioUsuarioPropiedadPref();
        $pref->setId_item($idPropPref);
        $pref->setId_item_usuario_objeto($idObjetoPref);
        $pref->setPropiedadVo(new PropiedadNombre('f_ini'));
        $pref->setOperadorVo(new OperadorPref('='));
        $pref->setValor($valorFiltro);
        $pref->setValor_old(true);
        $pref->setValor_new(false);

        $this->assertTrue($repository->Guardar($pref));
        $this->propiedadPrefsCreados[] = $pref;
    }

    /**
     * @return array{err_fila: string, bucle_infinito: bool}
     */
    private function ejecutarGenerarTabla(): array
    {
        /** @var AvisosGenerarTabla $useCase */
        $useCase = $GLOBALS['container']->get(AvisosGenerarTabla::class);

        return $useCase->execute('', ConfigGlobal::mi_region_dl());
    }

    /**
     * @param array{id_item_cambio: int, id_schema: int} $contexto
     * @return list<CambioUsuario>
     */
    private function buscarAvisosUsuario(array $contexto): array
    {
        /** @var CambioUsuarioRepositoryInterface $repository */
        $repository = $GLOBALS['container']->get(CambioUsuarioRepositoryInterface::class);

        $avisos = $repository->getCambiosUsuario([
            'id_item_cambio' => $contexto['id_item_cambio'],
            'id_schema_cambio' => $contexto['id_schema'],
            'id_usuario' => ConfigGlobal::mi_id_usuario(),
            'aviso_tipo' => 1,
            'sfsv' => ConfigGlobal::mi_sfsv(),
        ]);

        foreach ($avisos as $aviso) {
            $this->avisosCreados[] = $aviso;
        }

        return $avisos;
    }

    /**
     * @param array{id_item_cambio: int, id_schema: int} $contexto
     */
    private function assertCambioAnotado(array $contexto): void
    {
        /** @var CambioRepositoryInterface $cambioRepository */
        $cambioRepository = $GLOBALS['container']->get(CambioRepositoryInterface::class);

        $pendiente = false;
        foreach ($cambioRepository->getCambiosNuevos() as $cambio) {
            if ($cambio->getId_item_cambio() === $contexto['id_item_cambio']
                && $cambio->getId_schema() === $contexto['id_schema']
            ) {
                $pendiente = true;
                break;
            }
        }

        $this->assertFalse($pendiente, 'El cambio debería haberse marcado como anotado');
    }

    private function limpiarDatosDePrueba(): void
    {
        /** @var CambioUsuarioRepositoryInterface $avisoRepository */
        $avisoRepository = $GLOBALS['container']->get(CambioUsuarioRepositoryInterface::class);
        /** @var CambioUsuarioPropiedadPrefRepositoryInterface $propiedadRepository */
        $propiedadRepository = $GLOBALS['container']->get(CambioUsuarioPropiedadPrefRepositoryInterface::class);
        /** @var CambioUsuarioObjetoPrefRepositoryInterface $objetoRepository */
        $objetoRepository = $GLOBALS['container']->get(CambioUsuarioObjetoPrefRepositoryInterface::class);
        /** @var CambioDlRepositoryInterface $dlRepository */
        $dlRepository = $GLOBALS['container']->get(CambioDlRepositoryInterface::class);
        /** @var CambioAnotadoRepositoryInterface $anotadoRepository */
        $anotadoRepository = $GLOBALS['container']->get(CambioAnotadoRepositoryInterface::class);
        /** @var ActividadDlRepositoryInterface $actividadRepository */
        $actividadRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);

        foreach ($this->avisosCreados as $aviso) {
            $persistido = $avisoRepository->findById($aviso->getId_item());
            if ($persistido !== null) {
                $avisoRepository->Eliminar($persistido);
            }
        }

        foreach ($this->propiedadPrefsCreados as $pref) {
            $persistido = $propiedadRepository->findById($pref->getId_item());
            if ($persistido !== null) {
                $propiedadRepository->Eliminar($persistido);
            }
        }

        foreach ($this->objetoPrefsCreados as $pref) {
            $persistido = $objetoRepository->findById($pref->getId_item_usuario_objeto());
            if ($persistido !== null) {
                $objetoRepository->Eliminar($persistido);
            }
        }

        if ($this->cambioCreado !== null) {
            $anotadoRepository->setTabla((string) getenv('UBICACION'));
            $anotados = $anotadoRepository->getCambiosAnotados([
                'id_item_cambio' => $this->cambioCreado->getId_item_cambio(),
                'id_schema_cambio' => $this->cambioCreado->getId_schema(),
            ]);
            foreach ($anotados as $anotado) {
                $anotadoRepository->Eliminar($anotado);
            }

            $persistido = $dlRepository->findById($this->cambioCreado->getId_item_cambio());
            if ($persistido !== null) {
                $dlRepository->Eliminar($persistido);
            }
        }

        if ($this->idActiv > 0) {
            $actividad = $actividadRepository->findById($this->idActiv);
            if ($actividad !== null) {
                $actividadRepository->Eliminar($actividad);
            }
        }
    }

    /**
     * AvisosGenerarTabla recorre toda la cola de cambios no anotados.
     * Vaciamos la cola previa para que el test solo evalúe el cambio creado aquí.
     */
    private function anotarColaPendienteAlInicio(): void
    {
        /** @var CambioRepositoryInterface $cambioRepository */
        $cambioRepository = $GLOBALS['container']->get(CambioRepositoryInterface::class);
        /** @var CambioAnotadoRepositoryInterface $anotadoRepository */
        $anotadoRepository = $GLOBALS['container']->get(CambioAnotadoRepositoryInterface::class);
        $anotadoRepository->setTabla((string) getenv('UBICACION'));

        $serverRaw = getenv('DB_SERVER');
        $server = is_numeric($serverRaw) ? (int) $serverRaw : 0;

        foreach ($cambioRepository->getCambiosNuevos() as $cambio) {
            $anotados = $anotadoRepository->getCambiosAnotados([
                'id_item_cambio' => $cambio->getId_item_cambio(),
                'id_schema_cambio' => $cambio->getId_schema(),
            ]);
            if ($anotados !== []) {
                continue;
            }

            $anotado = new CambioAnotado();
            $anotado->setId_item((int) $anotadoRepository->getNewId());
            $anotado->setId_item_cambio($cambio->getId_item_cambio());
            $anotado->setId_schema_cambio($cambio->getId_schema());
            $anotado->setServer($server);
            $anotado->setAnotado(true);
            $anotadoRepository->Guardar($anotado);
        }
    }

    private function aplicarAppInstalada(string $nombre, bool $instalar, int $idApp): void
    {
        $aApps = $_SESSION['config']['a_apps'] ?? [];
        $installed = $_SESSION['config']['app_installed'] ?? [];
        if (!is_array($aApps)) {
            $aApps = [];
        }
        if (!is_array($installed)) {
            $installed = [];
        }

        if ($instalar) {
            $aApps[$nombre] = $idApp;
            if (!in_array($idApp, $installed, true)) {
                $installed[] = $idApp;
            }
        } else {
            $rmId = $aApps[$nombre] ?? null;
            unset($aApps[$nombre]);
            if ($rmId !== null) {
                $installed = array_values(array_diff($installed, [$rmId]));
            }
            $installed = array_values(array_diff($installed, [$idApp]));
        }

        $_SESSION['config']['a_apps'] = $aApps;
        $_SESSION['config']['app_installed'] = $installed;
    }

}
