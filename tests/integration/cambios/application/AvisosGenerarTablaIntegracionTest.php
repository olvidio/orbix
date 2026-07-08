<?php

declare(strict_types=1);

namespace Tests\integration\cambios\application;

use src\actividades\application\ActividadNueva;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadExRepositoryInterface;
use src\actividades\domain\contracts\ImportadaRepositoryInterface;
use src\actividades\domain\entity\Importada;
use src\actividades\domain\value_objects\ActividadNomText;
use src\actividades\domain\value_objects\ActividadTipoId;
use src\actividades\domain\value_objects\IdTablaCode;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\StatusId;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\cambios\application\AvisosGenerarListaData;
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
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\GlobalPdo;
use src\ubis\domain\value_objects\DelegacionCode;
use Tests\myTest;

/**
 * Integración del flujo: cambio en av_cambios_dl → AvisosGenerarTabla → av_cambios_usuario.
 *
 * Usa actividad real creada en BD, preferencias de objeto/propiedad y sin módulo procesos
 * (id_fase_ref = StatusId; json_fases = [id_status] como en RegistrarCambio).
 *
 * Los casos cross-dl con procesos instalado y id_schema de origen (p. ej. dlp/3005) están
 * en {@see test_generar_tabla_cambio_otra_dl_con_procesos_y_schema_origen_apunta()}.
 */
final class AvisosGenerarTablaIntegracionTest extends myTest
{
    private const APP_CAMBIOS_ID = 99002;

    private const APP_PROCESOS_ID = 99003;

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

    private bool $actividadExCreada = false;

    private bool $cambioPublicCreado = false;

    /** @var list<Importada> */
    private array $importadasCreadas = [];

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
        $this->actividadExCreada = false;
        $this->cambioPublicCreado = false;
        $this->importadasCreadas = [];
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

    public function test_generar_tabla_aviso_on_coincide_estado_actividad(): void
    {
        $this->prepararEntornoCambiosSinProcesos();
        $contexto = $this->crearActividadYCambio([
            'propiedad' => 'nom_activ',
            'valor_old' => 'nombre anterior',
            'valor_new' => 'nombre nuevo',
        ], 'ActividadDl', statusActividad: StatusId::PROYECTO, jsonFasesComoRegistrarCambio: true);
        $this->crearPreferenciaObjeto($contexto, 'Actividad', idFaseRef: StatusId::PROYECTO, avisoOn: true, avisoOff: false);

        $resultado = $this->ejecutarGenerarTabla();

        $this->assertFalse($resultado['bucle_infinito']);
        $this->assertCount(1, $this->buscarAvisosUsuario($contexto));
        $this->assertCambioAnotado($contexto);
    }

    public function test_generar_tabla_aviso_on_no_coincide_estado_actividad(): void
    {
        $this->prepararEntornoCambiosSinProcesos();
        $contexto = $this->crearActividadYCambio([
            'propiedad' => 'nom_activ',
            'valor_old' => 'nombre anterior',
            'valor_new' => 'nombre nuevo',
        ], 'ActividadDl', statusActividad: StatusId::ACTUAL, jsonFasesComoRegistrarCambio: true);
        $this->crearPreferenciaObjeto($contexto, 'Actividad', idFaseRef: StatusId::PROYECTO, avisoOn: true, avisoOff: false);

        $resultado = $this->ejecutarGenerarTabla();

        $this->assertFalse($resultado['bucle_infinito']);
        $this->assertSame([], $this->buscarAvisosUsuario($contexto));
        $this->assertCambioAnotado($contexto);
    }

    public function test_generar_tabla_aviso_off_actividad_fuera_del_estado(): void
    {
        $this->prepararEntornoCambiosSinProcesos();
        $contexto = $this->crearActividadYCambio([
            'propiedad' => 'nom_activ',
            'valor_old' => 'nombre anterior',
            'valor_new' => 'nombre nuevo',
        ], 'ActividadDl', statusActividad: StatusId::ACTUAL, jsonFasesComoRegistrarCambio: true);
        $this->crearPreferenciaObjeto($contexto, 'Actividad', idFaseRef: StatusId::PROYECTO, avisoOn: false, avisoOff: true);

        $resultado = $this->ejecutarGenerarTabla();

        $this->assertFalse($resultado['bucle_infinito']);
        $this->assertCount(1, $this->buscarAvisosUsuario($contexto));
        $this->assertCambioAnotado($contexto);
    }

    public function test_generar_tabla_aviso_off_actividad_en_el_estado(): void
    {
        $this->prepararEntornoCambiosSinProcesos();
        $contexto = $this->crearActividadYCambio([
            'propiedad' => 'nom_activ',
            'valor_old' => 'nombre anterior',
            'valor_new' => 'nombre nuevo',
        ], 'ActividadDl', statusActividad: StatusId::PROYECTO, jsonFasesComoRegistrarCambio: true);
        $this->crearPreferenciaObjeto($contexto, 'Actividad', idFaseRef: StatusId::PROYECTO, avisoOn: false, avisoOff: true);

        $resultado = $this->ejecutarGenerarTabla();

        $this->assertFalse($resultado['bucle_infinito']);
        $this->assertSame([], $this->buscarAvisosUsuario($contexto));
        $this->assertCambioAnotado($contexto);
    }

    public function test_generar_tabla_asistente_aviso_on_por_estado(): void
    {
        $this->prepararEntornoCambiosSinProcesos();
        $contexto = $this->crearActividadYCambio([
            'propiedad' => 'id_nom',
            'valor_old' => '10011',
            'valor_new' => '10012',
        ], 'Asistente', statusActividad: StatusId::PROYECTO, jsonFasesComoRegistrarCambio: true);
        $this->crearPreferenciaObjeto($contexto, 'Asistente', idFaseRef: StatusId::PROYECTO, avisoOn: true, avisoOff: false);

        $resultado = $this->ejecutarGenerarTabla();

        $this->assertFalse($resultado['bucle_infinito']);
        $this->assertCount(1, $this->buscarAvisosUsuario($contexto));
        $this->assertCambioAnotado($contexto);
    }

    public function test_generar_tabla_cargo_sacd_aviso_off_por_estado(): void
    {
        $this->prepararEntornoCambiosSinProcesos();
        $contexto = $this->crearActividadYCambio([
            'propiedad' => 'id_nom',
            'valor_old' => '20011',
            'valor_new' => '20012',
        ], 'ActividadCargoSacd', statusActividad: StatusId::ACTUAL, jsonFasesComoRegistrarCambio: true);
        $this->crearPreferenciaObjeto($contexto, 'ActividadCargoSacd', idFaseRef: StatusId::PROYECTO, avisoOn: false, avisoOff: true);

        $resultado = $this->ejecutarGenerarTabla();

        $this->assertFalse($resultado['bucle_infinito']);
        $this->assertCount(1, $this->buscarAvisosUsuario($contexto));
        $this->assertCambioAnotado($contexto);
    }

    public function test_generar_tabla_sin_importada_no_apunta_cambio_otra_dl(): void
    {
        $this->prepararEntornoCambiosSinProcesos();
        $contexto = $this->crearCambioPublicOtraDlImportada([
            'propiedad' => 'precio',
            'valor_old' => '10',
            'valor_new' => '15',
        ], importada: false);
        $this->crearPreferenciaObjetoOtrasDl(
            $contexto,
            'Actividad',
            idFaseRef: StatusId::ACTUAL,
            avisoOn: true,
            avisoOff: false,
        );

        $resultado = $this->ejecutarGenerarTabla();

        $this->assertFalse($resultado['bucle_infinito']);
        $this->assertSame([], $this->buscarAvisosUsuario($contexto));
        $this->assertCambioAnotado($contexto);
    }

    public function test_generar_tabla_cambio_public_otra_dl_importada_apunta(): void
    {
        $this->prepararEntornoCambiosSinProcesos();
        $contexto = $this->crearCambioPublicOtraDlImportada([
            'propiedad' => 'precio',
            'valor_old' => '20',
            'valor_new' => '25',
        ]);
        $this->crearPreferenciaObjetoOtrasDl(
            $contexto,
            'Actividad',
            idFaseRef: StatusId::ACTUAL,
            avisoOn: true,
            avisoOff: false,
        );

        $resultado = $this->ejecutarGenerarTabla();

        $this->assertFalse($resultado['bucle_infinito']);
        $this->assertCount(1, $this->buscarAvisosUsuario($contexto));
        $this->assertCambioAnotado($contexto);
    }

    public function test_generar_tabla_cambio_public_otra_dl_importada_aparece_en_lista(): void
    {
        $this->prepararEntornoCambiosSinProcesos();
        $contexto = $this->crearCambioPublicOtraDlImportada([
            'propiedad' => 'precio',
            'valor_old' => '30',
            'valor_new' => '35',
        ]);
        $this->crearPreferenciaObjetoOtrasDl(
            $contexto,
            'Actividad',
            idFaseRef: StatusId::ACTUAL,
            avisoOn: true,
            avisoOff: false,
        );
        $this->ejecutarGenerarTabla();

        /** @var AvisosGenerarListaData $listaData */
        $listaData = $GLOBALS['container']->get(AvisosGenerarListaData::class);
        $lista = $listaData->execute(['is_admin' => false]);

        $this->assertSame('', $lista['error']);
        $this->assertNotSame([], $lista['a_valores']);
        $textos = array_column($lista['a_valores'], 3);
        $this->assertTrue(
            (bool) array_filter($textos, static fn (string $txt): bool => str_contains($txt, 'precio')),
            'La lista de avisos debería incluir el cambio de precio de la actividad importada',
        );
    }

    /**
     * Regresión producción (dlp→dlb): cambio en av_cambios con id_schema del origen (≠3000),
     * actividad Ex importada, procesos instalado y json_fases con ids de fase distintos de
     * id_fase_ref en la preferencia. Antes no apuntaba en av_cambios_usuario.
     */
    public function test_generar_tabla_cambio_otra_dl_con_procesos_y_schema_origen_apunta(): void
    {
        $this->prepararEntornoCambiosConProcesos();
        $fases = $this->fasesProcesoParaRegresionOtraDl();
        if ($fases === null) {
            $this->markTestSkipped('No hay tareas de proceso suficientes para tipo 271000');
        }

        $contexto = $this->crearCambioOrigenOtraDlImportada(
            [
                'propiedad' => 'precio',
                'valor_old' => '40',
                'valor_new' => '45',
            ],
            jsonFasesIds: $fases['json_fases_cambio'],
        );
        $this->assertNotSame(
            3000,
            $contexto['id_schema'],
            'El cambio cross-dl debe conservar el id_schema del origen, no el 3000 de public',
        );
        $this->assertNotContains(
            $fases['id_fase_ref_pref'],
            $fases['json_fases_cambio'],
            'json_fases del cambio no debe incluir id_fase_ref (escenario que fallaba antes)',
        );

        $this->crearPreferenciaObjetoOtrasDl(
            $contexto,
            'Actividad',
            idFaseRef: $fases['id_fase_ref_pref'],
            avisoOn: true,
            avisoOff: false,
        );

        $resultado = $this->ejecutarGenerarTabla();

        $this->assertFalse($resultado['bucle_infinito']);
        $avisos = $this->buscarAvisosUsuario($contexto);
        $this->assertCount(1, $avisos, 'Debería apuntar en av_cambios_usuario con id_schema del origen');
        $this->assertSame($contexto['id_schema'], $avisos[0]->getId_schema_cambio());
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
        $this->aplicarAppInstalada('procesos', false, self::APP_PROCESOS_ID);
        $this->assertTrue(ConfigGlobal::is_app_installed('cambios'));
        $this->assertFalse(ConfigGlobal::is_app_installed('procesos'));
        $this->anotarColaPendienteAlInicio();
    }

    private function prepararEntornoCambiosConProcesos(): void
    {
        $this->aplicarAppInstalada('cambios', true, self::APP_CAMBIOS_ID);
        $this->aplicarAppInstalada('procesos', true, self::APP_PROCESOS_ID);
        $this->assertTrue(ConfigGlobal::is_app_installed('cambios'));
        $this->assertTrue(ConfigGlobal::is_app_installed('procesos'));
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
        int $statusActividad = StatusId::PROYECTO,
        bool $jsonFasesComoRegistrarCambio = false,
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
            'status' => $statusActividad,
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

        $jsonFases = $jsonFasesComoRegistrarCambio ? ['0' => $statusActividad] : [];

        $cambio = new Cambio();
        $cambio->setId_item_cambio($idItemCambio);
        $cambio->setTipoCambioVo(new TipoCambioId(Cambio::TIPO_CMB_UPDATE));
        $cambio->setId_activ($this->idActiv);
        $cambio->setIdTipoActivVo(new ActividadTipoId($idTipo));
        $cambio->setJson_fases_sv($jsonFases);
        $cambio->setJson_fases_sf($jsonFases);
        $cambio->setIdStatusVo(new StatusId($statusActividad));
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
     * Cambio en ONLY public.av_cambios (sincronizado desde otra dl), con actividad Ex importada aquí.
     *
     * @param array{propiedad: string, valor_old: string, valor_new: string} $cambioDatos
     * @return array{
     *     id_activ: int,
     *     id_tipo_activ: int,
     *     dl_org: string,
     *     id_item_cambio: int,
     *     id_schema: int,
     * }
     */
    private function crearCambioPublicOtraDlImportada(
        array $cambioDatos,
        bool $importada = true,
    ): array {
        $contextoActividad = $this->crearActividadExOtraDl();
        if ($importada) {
            $this->marcarActividadComoImportada($contextoActividad['id_activ']);
        }

        /** @var CambioRepositoryInterface $publicRepository */
        $publicRepository = $GLOBALS['container']->get(CambioRepositoryInterface::class);
        $idItemCambio = (int) $publicRepository->getNewId();
        $this->assertGreaterThan(0, $idItemCambio);

        $jsonFases = ['0' => StatusId::ACTUAL];

        $cambio = new Cambio();
        $cambio->setId_item_cambio($idItemCambio);
        $cambio->setTipoCambioVo(new TipoCambioId(Cambio::TIPO_CMB_UPDATE));
        $cambio->setId_activ($contextoActividad['id_activ']);
        $cambio->setIdTipoActivVo(new ActividadTipoId($contextoActividad['id_tipo_activ']));
        $cambio->setJson_fases_sv($jsonFases);
        $cambio->setJson_fases_sf($jsonFases);
        $cambio->setIdStatusVo(new StatusId(StatusId::ACTUAL));
        $cambio->setDlOrgVo(new DelegacionCode($contextoActividad['dl_org']));
        $cambio->setObjetoVo(new ObjetoNombre('ActividadEx'));
        $cambio->setPropiedadVo(new PropiedadNombre($cambioDatos['propiedad']));
        $cambio->setValor_old($cambioDatos['valor_old']);
        $cambio->setValor_new($cambioDatos['valor_new']);
        $cambio->setQuien_cambia(ConfigGlobal::mi_id_usuario());
        $cambio->setSfsv_quien_cambia(ConfigGlobal::mi_sfsv());
        $cambio->setTimestamp_cambio(new DateTimeLocal('now'));

        $this->assertTrue($publicRepository->Guardar($cambio));

        $persistido = $publicRepository->findById($idItemCambio);
        $this->assertNotNull($persistido);
        $this->cambioCreado = $persistido;
        $this->cambioPublicCreado = true;

        /** @var CambioDlRepositoryInterface $dlRepository */
        $dlRepository = $GLOBALS['container']->get(CambioDlRepositoryInterface::class);
        $this->assertNull(
            $dlRepository->findById($idItemCambio),
            'El cambio cross-dl debe existir solo en public.av_cambios',
        );

        return [
            'id_activ' => $contextoActividad['id_activ'],
            'id_tipo_activ' => $contextoActividad['id_tipo_activ'],
            'dl_org' => $contextoActividad['dl_org'],
            'id_item_cambio' => $idItemCambio,
            'id_schema' => $persistido->getId_schema(),
        ];
    }

    /**
     * Cambio cross-dl insertado en public.av_cambios con id_schema del origen (herencia),
     * no copiado en av_cambios_dl local. Simula sincronización desde otra dl (p. ej. dlp).
     *
     * @param array{propiedad: string, valor_old: string, valor_new: string} $cambioDatos
     * @param list<int> $jsonFasesIds ids de fase del proceso anotados en el cambio
     * @return array{
     *     id_activ: int,
     *     id_tipo_activ: int,
     *     dl_org: string,
     *     id_item_cambio: int,
     *     id_schema: int,
     * }
     */
    private function crearCambioOrigenOtraDlImportada(
        array $cambioDatos,
        array $jsonFasesIds,
    ): array {
        $contextoActividad = $this->crearActividadExOtraDl();
        $this->marcarActividadComoImportada($contextoActividad['id_activ']);

        /** @var CambioRepositoryInterface $publicRepository */
        $publicRepository = $GLOBALS['container']->get(CambioRepositoryInterface::class);
        $idItemCambio = (int) $publicRepository->getNewId();
        $this->assertGreaterThan(0, $idItemCambio);

        $idSchemaOrigen = $this->idSchemaOrigenDistintoDeLocal();
        $jsonFases = [];
        foreach (array_values($jsonFasesIds) as $i => $faseId) {
            $jsonFases[(string) $i] = $faseId;
        }

        $this->insertarCambioEnPublicAvCambios(
            $idSchemaOrigen,
            $idItemCambio,
            $contextoActividad,
            $cambioDatos,
            $jsonFases,
        );

        $cCambios = $publicRepository->getCambios([
            'id_schema' => $idSchemaOrigen,
            'id_item_cambio' => $idItemCambio,
        ]);
        $this->assertCount(1, $cCambios);
        $persistido = $cCambios[0];
        $this->cambioCreado = $persistido;
        $this->cambioPublicCreado = true;

        /** @var CambioDlRepositoryInterface $dlRepository */
        $dlRepository = $GLOBALS['container']->get(CambioDlRepositoryInterface::class);
        $this->assertNull(
            $dlRepository->findById($idItemCambio),
            'El cambio cross-dl no debe estar copiado en av_cambios_dl local',
        );

        return [
            'id_activ' => $contextoActividad['id_activ'],
            'id_tipo_activ' => $contextoActividad['id_tipo_activ'],
            'dl_org' => $contextoActividad['dl_org'],
            'id_item_cambio' => $idItemCambio,
            'id_schema' => $idSchemaOrigen,
        ];
    }

    /**
     * @param array{id_activ: int, id_tipo_activ: int, dl_org: string} $contextoActividad
     * @param array{propiedad: string, valor_old: string, valor_new: string} $cambioDatos
     * @param array<string, int> $jsonFases
     */
    private function insertarCambioEnPublicAvCambios(
        int $idSchemaOrigen,
        int $idItemCambio,
        array $contextoActividad,
        array $cambioDatos,
        array $jsonFases,
    ): void {
        $oDbl = GlobalPdo::get('oDBPC');
        $jsonSv = json_encode($jsonFases, JSON_THROW_ON_ERROR);
        $jsonSf = $jsonSv;
        $sql = 'INSERT INTO public.av_cambios (
                    id_schema, id_item_cambio, id_tipo_cambio, id_activ, id_tipo_activ,
                    json_fases_sv, json_fases_sf, id_status, dl_org, objeto, propiedad,
                    valor_old, valor_new, quien_cambia, sfsv_quien_cambia, timestamp_cambio
                ) VALUES (
                    :id_schema, :id_item_cambio, :id_tipo_cambio, :id_activ, :id_tipo_activ,
                    :json_fases_sv::json, :json_fases_sf::json, :id_status, :dl_org, :objeto, :propiedad,
                    :valor_old, :valor_new, :quien_cambia, :sfsv_quien_cambia, :timestamp_cambio
                )';
        $stmt = $oDbl->prepare($sql);
        $this->assertNotFalse($stmt);
        $ok = $stmt->execute([
            'id_schema' => $idSchemaOrigen,
            'id_item_cambio' => $idItemCambio,
            'id_tipo_cambio' => Cambio::TIPO_CMB_UPDATE,
            'id_activ' => $contextoActividad['id_activ'],
            'id_tipo_activ' => $contextoActividad['id_tipo_activ'],
            'json_fases_sv' => $jsonSv,
            'json_fases_sf' => $jsonSf,
            'id_status' => StatusId::ACTUAL,
            'dl_org' => $contextoActividad['dl_org'],
            'objeto' => 'ActividadEx',
            'propiedad' => $cambioDatos['propiedad'],
            'valor_old' => $cambioDatos['valor_old'],
            'valor_new' => $cambioDatos['valor_new'],
            'quien_cambia' => ConfigGlobal::mi_id_usuario(),
            'sfsv_quien_cambia' => ConfigGlobal::mi_sfsv(),
            'timestamp_cambio' => (new DateTimeLocal('now'))->format('Y-m-d H:i:s'),
        ]);
        $this->assertTrue($ok);
    }

    private function idSchemaOrigenDistintoDeLocal(): int
    {
        $miSchema = ConfigGlobal::mi_id_schema();
        $oDbl = GlobalPdo::get('oDBPC');
        $stmt = $oDbl->prepare(
            'SELECT id FROM public.db_idschema WHERE id <> :mi AND id <> 3000 ORDER BY id LIMIT 1',
        );
        $this->assertNotFalse($stmt);
        $stmt->execute(['mi' => $miSchema]);
        $id = $stmt->fetchColumn();
        if ($id === false) {
            // Entorno mínimo de test: usar un id_schema ficticio distinto de 3000 y del local.
            return $miSchema > 3000 ? 3000 + 1 : 3005;
        }

        return (int) $id;
    }

    /**
     * Fase de referencia (status ACTUAL) + otras fases para json_fases del cambio.
     *
     * @return array{id_fase_ref_pref: int, json_fases_cambio: list<int>}|null
     */
    private function fasesProcesoParaRegresionOtraDl(): ?array
    {
        $idTipo = 271000;
        /** @var TipoDeActividadRepositoryInterface $tipoRepository */
        $tipoRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        /** @var TareaProcesoRepositoryInterface $tareaRepository */
        $tareaRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);

        $cTiposActividad = $tipoRepository->getTiposDeActividades(['id_tipo_activ' => $idTipo]);
        if ($cTiposActividad === []) {
            return null;
        }

        $idTipoProceso = $cTiposActividad[0]->getId_tipo_proceso(ConfigGlobal::mi_sfsv());
        $cTareasProceso = $tareaRepository->getTareasProceso(['id_tipo_proceso' => $idTipoProceso]);
        if (count($cTareasProceso) < 2) {
            return null;
        }

        $idFaseRefPref = 0;
        $jsonFasesCambio = [];
        foreach ($cTareasProceso as $oTarea) {
            $idFase = $oTarea->getId_fase();
            if ($idFase <= 0) {
                continue;
            }
            if ($idFaseRefPref === 0 && $oTarea->getStatus() === StatusId::ACTUAL) {
                $idFaseRefPref = $idFase;
                continue;
            }
            if ($idFase !== $idFaseRefPref && !in_array($idFase, $jsonFasesCambio, true)) {
                $jsonFasesCambio[] = $idFase;
            }
        }

        if ($idFaseRefPref === 0 || $jsonFasesCambio === []) {
            return null;
        }

        return [
            'id_fase_ref_pref' => $idFaseRefPref,
            'json_fases_cambio' => array_slice($jsonFasesCambio, 0, 2),
        ];
    }

    /**
     * @return array{id_activ: int, id_tipo_activ: int, dl_org: string}
     */
    private function crearActividadExOtraDl(): array
    {
        $idTipo = 271000;
        $dlOrg = $this->otraDlOrgParaTest();
        $statusActividad = StatusId::ACTUAL;
        $nom = 'avisos_ex_' . uniqid('', true);

        /** @var ActividadExRepositoryInterface $exRepository */
        $exRepository = $GLOBALS['container']->get(ActividadExRepositoryInterface::class);
        $newId = (int) $exRepository->getNewId();
        $this->assertGreaterThan(0, $newId);
        $this->idActiv = (int) $exRepository->getNewIdActividad($newId);
        $this->assertGreaterThan(0, $this->idActiv);

        $actividad = new \src\actividades\domain\entity\ActividadAll();
        $actividad->setId_activ($this->idActiv);
        $actividad->setIdTablaVo(new IdTablaCode('ex'));
        $actividad->setDlOrgVo(new DelegacionCode($dlOrg));
        $actividad->setIdTipoActivVo(new ActividadTipoId($idTipo));
        $actividad->setNomActivVo(new ActividadNomText($nom));
        $actividad->setStatusVo(new StatusId($statusActividad));
        $actividad->setF_ini(new DateTimeLocal('2099-06-01'));
        $actividad->setF_fin(new DateTimeLocal('2099-06-02'));
        $actividad->setPublicado(true);
        $actividad->setNivel_stgr(NivelStgrId::N);
        $actividad->setId_repeticion(1);

        $this->assertTrue($exRepository->Guardar($actividad));
        $this->actividadExCreada = true;

        return [
            'id_activ' => $this->idActiv,
            'id_tipo_activ' => $idTipo,
            'dl_org' => $dlOrg,
        ];
    }

    private function otraDlOrgParaTest(): string
    {
        $miDele = ConfigGlobal::mi_dele();

        return $miDele === 'dla' ? 'dlb' : 'dla';
    }

    private function marcarActividadComoImportada(int $idActiv): void
    {
        /** @var ImportadaRepositoryInterface $repository */
        $repository = $GLOBALS['container']->get(ImportadaRepositoryInterface::class);
        $importada = new Importada();
        $importada->setId_activ($idActiv);
        $this->assertTrue($repository->Guardar($importada));
        $this->importadasCreadas[] = $importada;
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
    private function crearPreferenciaObjetoOtrasDl(
        array $contexto,
        string $objetoPref,
        int $idFaseRef = StatusId::PROYECTO,
        bool $avisoOn = true,
        bool $avisoOff = false,
    ): int {
        /** @var CambioUsuarioObjetoPrefRepositoryInterface $repository */
        $repository = $GLOBALS['container']->get(CambioUsuarioObjetoPrefRepositoryInterface::class);
        $idObjetoPref = (int) $repository->getNewId();
        $this->assertGreaterThan(0, $idObjetoPref);

        $pref = new CambioUsuarioObjetoPref();
        $pref->setId_item_usuario_objeto($idObjetoPref);
        $pref->setId_usuario(ConfigGlobal::mi_id_usuario());
        $pref->setDl_org('x');
        $pref->setId_tipo_activ_txt((string) $contexto['id_tipo_activ']);
        $pref->setId_fase_ref($idFaseRef);
        $pref->setAviso_on($avisoOn);
        $pref->setAviso_off($avisoOff);
        $pref->setAviso_outdate(false);
        $pref->setObjetoVo(new ObjetoNombre($objetoPref));
        $pref->setAvisoTipoVo(new AvisoTipoId(1));

        $this->assertTrue($repository->Guardar($pref));
        $this->objetoPrefsCreados[] = $pref;

        return $idObjetoPref;
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
        int $idFaseRef = StatusId::PROYECTO,
        bool $avisoOn = true,
        bool $avisoOff = false,
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
        $pref->setId_fase_ref($idFaseRef);
        $pref->setAviso_on($avisoOn);
        $pref->setAviso_off($avisoOff);
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
        /** @var CambioRepositoryInterface $cambioRepository */
        $cambioRepository = $GLOBALS['container']->get(CambioRepositoryInterface::class);
        /** @var CambioAnotadoRepositoryInterface $anotadoRepository */
        $anotadoRepository = $GLOBALS['container']->get(CambioAnotadoRepositoryInterface::class);
        /** @var ActividadDlRepositoryInterface $actividadRepository */
        $actividadRepository = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
        /** @var ActividadExRepositoryInterface $actividadExRepository */
        $actividadExRepository = $GLOBALS['container']->get(ActividadExRepositoryInterface::class);
        /** @var ImportadaRepositoryInterface $importadaRepository */
        $importadaRepository = $GLOBALS['container']->get(ImportadaRepositoryInterface::class);

        foreach ($this->importadasCreadas as $importada) {
            $persistido = $importadaRepository->findById($importada->getId_activ());
            if ($persistido !== null) {
                $importadaRepository->Eliminar($persistido);
            }
        }

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

            $persistido = $this->cambioPublicCreado
                ? $cambioRepository->findById($this->cambioCreado->getId_item_cambio())
                : $dlRepository->findById($this->cambioCreado->getId_item_cambio());
            if ($persistido !== null) {
                if ($this->cambioPublicCreado) {
                    $cambioRepository->Eliminar($persistido);
                } else {
                    $dlRepository->Eliminar($persistido);
                }
            }
        }

        if ($this->idActiv > 0) {
            if ($this->actividadExCreada) {
                $actividad = $actividadExRepository->findById($this->idActiv);
                if ($actividad !== null) {
                    $actividadExRepository->Eliminar($actividad);
                }
            } else {
                $actividad = $actividadRepository->findById($this->idActiv);
                if ($actividad !== null) {
                    $actividadRepository->Eliminar($actividad);
                }
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
