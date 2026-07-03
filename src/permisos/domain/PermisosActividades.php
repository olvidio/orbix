<?php

namespace src\permisos\domain;

use PDO;
use PDOStatement;
use RuntimeException;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\procesos\domain\PermAccion;
use src\procesos\domain\value_objects\FaseId;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\logging\GestorErrores;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Matriz de permisos por tipo de actividad + contexto de la actividad actual en sesión.
 *
 * **Dirección de arquitectura:** conviene separar (1) el *read model* cacheado en sesión
 * (solo `aPermDl` / `aPermOtras` y lectura sin I/O) de (2) la resolución que use
 * contenedor/repositorios (actividad por id, fases completadas, procesos para crear),
 * que debe vivir en `src/` y exponerse al `frontend/` vía `PostRequest` o DTOs.
 * Ver `agents.md` § «Permisos de actividad en sesión».
 *
 * Instanciación (sesión):
 *
 *    DependencyResolver::make(PermisosActividades::class, ['idUsuario' => ConfigGlobal::mi_id_usuario()]);
 *
 * Estructura de l'array:
 *    - aAfecta: el nom i corresponent integer de les propietats a les que afecta.
 *    - 2 coponents: aPermDl i aPermOtras, segons siguin els permisos per les activitats de la dl o la resta.
 *      Cada un d'aquests vectors es composa de:
 *        a) primer component: id_tipo_activ_txt = '12....'
 *            a1) iAfecta
 *            a2) id_tipo_proceso
 *            a3) iFase
 *            a4) permiso
 *
 *            $this->aDades[$iAfecta][$id_tipo_proceso][$iFase]=$iPerm;
 *
 *
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/11/2010
 */
class PermisosActividades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /**
     * Perm de PermisoActividad
     *
     * @var array
     */
    /** @var array<string, int> */
    public const AFECTA = [
        'datos' => 1,
        'economic' => 2,
        'sacd' => 4,
        'ctr' => 8,
        'id_tarifa' => 16,
        'cargos' => 32,
        'asistentes' => 64,
        'asistentesSacd' => 128,
    ];

    /** @var array<string, XResto> */
    protected array $aPermDl = [];

    /** @var array<string, XResto> */
    protected array $aPermOtras = [];
    /**
     * Per saber a quina activitat fa referència.
     */
    protected string $sid_tipo_activ = '';

    /**
     * Id_activ de PermisoActividad
     */
    protected int $iid_activ = 0;
    /**
     * Id_tipo_proceso de PermisoActividad
     */
    protected int $iid_tipo_proceso = 0;
    /**
     * propia de PermisoActividad
     */
    protected bool $bpropia = false;
    /**
     * número de orden de la fase actual
     */
    protected int $iid_fase = 0;
    /**
     * si ha llegado al final.
     */
    protected bool $btop = false;

    /** @var list<int> */
    private array $aFasesCompletadas = [];

    /**
     * Contexto de tipo y delegación ya resuelto (p. ej. pasado por el caller o
     * tras cargar por id) para reutilizar sin nueva consulta a repositorio.
     */
    private ?string $setActividadContextTipo = null;

    private ?string $setActividadContextDlOrg = null;

    protected int $idUsuario = 0;

    /** Repositorios no serializables (PDO); se reinyectan tras deserializar sesión. */
    private ?UsuarioGrupoRepositoryInterface $usuarioGrupoRepository = null;
    private ?ActividadAllRepositoryInterface $actividadAllRepository = null;
    private ?ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository = null;
    private ?TipoDeActividadRepositoryInterface $tipoDeActividadRepository = null;
    private ?TareaProcesoRepositoryInterface $tareaProcesoRepository = null;

    /* METODES ----------------------------------------------------------------- */
    public function __construct(
        UsuarioGrupoRepositoryInterface $usuarioGrupoRepository,
        ActividadAllRepositoryInterface $actividadAllRepository,
        ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
        TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
        TareaProcesoRepositoryInterface $tareaProcesoRepository,
        int $idUsuario,
    ) {
        $this->usuarioGrupoRepository = $usuarioGrupoRepository;
        $this->actividadAllRepository = $actividadAllRepository;
        $this->actividadProcesoTareaRepository = $actividadProcesoTareaRepository;
        $this->tipoDeActividadRepository = $tipoDeActividadRepository;
        $this->tareaProcesoRepository = $tareaProcesoRepository;
        $this->idUsuario = $idUsuario;
        $sCondicion_usuario = "u.id_usuario=$idUsuario";
        $cGrupos = $this->getUsuarioGrupoRepository()->getUsuariosGrupos(['id_usuario' => $idUsuario]);
        if (count($cGrupos) > 0) {
            foreach ($cGrupos as $oUsuarioGrupo) {
                $id = $oUsuarioGrupo->getId_grupo();
                $sCondicion_usuario .= " OR u.id_usuario=$id";
            }
            $sCondicion_usuario = "($sCondicion_usuario)";
        }
        $this->carregar($sCondicion_usuario, 't');
        $this->carregar($sCondicion_usuario, 'f');
    }

    /**
     * Solo estado de permisos en sesión; los repositorios (con PDO) se excluyen.
     *
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        return [
            'idUsuario' => $this->idUsuario,
            'aPermDl' => $this->aPermDl,
            'aPermOtras' => $this->aPermOtras,
            'sid_tipo_activ' => $this->sid_tipo_activ,
            'iid_activ' => $this->iid_activ,
            'iid_tipo_proceso' => $this->iid_tipo_proceso,
            'bpropia' => $this->bpropia,
            'iid_fase' => $this->iid_fase,
            'btop' => $this->btop,
            'aFasesCompletadas' => $this->aFasesCompletadas,
            'setActividadContextTipo' => $this->setActividadContextTipo,
            'setActividadContextDlOrg' => $this->setActividadContextDlOrg,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): void
    {
        $this->idUsuario = self::dbRowInt($data['idUsuario'] ?? 0);
        $this->aPermDl = self::restoreXRestoMap($data['aPermDl'] ?? null);
        $this->aPermOtras = self::restoreXRestoMap($data['aPermOtras'] ?? null);
        $this->sid_tipo_activ = self::dbRowString($data['sid_tipo_activ'] ?? '');
        $this->iid_activ = self::dbRowInt($data['iid_activ'] ?? 0);
        $this->iid_tipo_proceso = self::dbRowInt($data['iid_tipo_proceso'] ?? 0);
        $this->bpropia = (bool) ($data['bpropia'] ?? false);
        $this->iid_fase = self::dbRowInt($data['iid_fase'] ?? 0);
        $this->btop = (bool) ($data['btop'] ?? false);
        $this->aFasesCompletadas = self::restoreIntList($data['aFasesCompletadas'] ?? null);
        $this->setActividadContextTipo = self::optionalString($data['setActividadContextTipo'] ?? null);
        $this->setActividadContextDlOrg = self::optionalString($data['setActividadContextDlOrg'] ?? null);
        $this->usuarioGrupoRepository = null;
        $this->actividadAllRepository = null;
        $this->actividadProcesoTareaRepository = null;
        $this->tipoDeActividadRepository = null;
        $this->tareaProcesoRepository = null;
    }

    private function ensureRepositories(): void
    {
        if ($this->actividadAllRepository !== null) {
            return;
        }
        $this->usuarioGrupoRepository = DependencyResolver::get(UsuarioGrupoRepositoryInterface::class);
        $this->actividadAllRepository = DependencyResolver::get(ActividadAllRepositoryInterface::class);
        $this->actividadProcesoTareaRepository = DependencyResolver::get(
            ActividadProcesoTareaRepositoryInterface::class
        );
        $this->tipoDeActividadRepository = DependencyResolver::get(TipoDeActividadRepositoryInterface::class);
        $this->tareaProcesoRepository = DependencyResolver::get(TareaProcesoRepositoryInterface::class);
    }

    private function getUsuarioGrupoRepository(): UsuarioGrupoRepositoryInterface
    {
        $this->ensureRepositories();

        return $this->usuarioGrupoRepository
            ?? throw new RuntimeException('PermisosActividades: UsuarioGrupoRepository no disponible.');
    }

    private function getActividadAllRepository(): ActividadAllRepositoryInterface
    {
        $this->ensureRepositories();

        return $this->actividadAllRepository
            ?? throw new RuntimeException('PermisosActividades: ActividadAllRepository no disponible.');
    }

    private function getActividadProcesoTareaRepository(): ActividadProcesoTareaRepositoryInterface
    {
        $this->ensureRepositories();

        return $this->actividadProcesoTareaRepository
            ?? throw new RuntimeException('PermisosActividades: ActividadProcesoTareaRepository no disponible.');
    }

    private function getTipoDeActividadRepository(): TipoDeActividadRepositoryInterface
    {
        $this->ensureRepositories();

        return $this->tipoDeActividadRepository
            ?? throw new RuntimeException('PermisosActividades: TipoDeActividadRepository no disponible.');
    }

    private function getTareaProcesoRepository(): TareaProcesoRepositoryInterface
    {
        $this->ensureRepositories();

        return $this->tareaProcesoRepository
            ?? throw new RuntimeException('PermisosActividades: TareaProcesoRepository no disponible.');
    }

    private function carregar(string $sCondicion_usuario, string $dl_propia): bool
    {
        $oDbl = GlobalPdo::get('oDBE');
        // Orden: los usuarios empiezan por 4, los grupos por 5.
        // Al ordenar, el usuario (queda el último) sobreescribe al grupo.
        // Los grupos, como puede haber más de uno los ordeno por orden alfabético DESC (prioridad A-Z).
        $Qry = "SELECT DISTINCT p.*, SUBSTRING( p.id_usuario::text, 1, 1 ) as orden, u.usuario
			FROM aux_usuarios_perm p JOIN aux_grupos_y_usuarios u USING (id_usuario)
			WHERE $sCondicion_usuario AND dl_propia='$dl_propia' 
			ORDER BY orden DESC, usuario DESC
			";
        $stmt = $oDbl->query($Qry);
        if (!$stmt instanceof PDOStatement) {
            $sClauError = 'PermisosActividades.carregar';
            if (isset($_SESSION['oGestorErrores']) && $_SESSION['oGestorErrores'] instanceof GestorErrores) {
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, (string) __LINE__, __FILE__);
            }
            return false;
        }
        foreach ($stmt as $row) {
            if (!is_array($row)) {
                continue;
            }
            $id_tipo_activ_txt = self::dbRowString($row['id_tipo_activ_txt'] ?? '');
            $fase_refRaw = $row['fase_ref'] ?? 0;
            $fase_ref = is_int($fase_refRaw) || is_string($fase_refRaw)
                ? $fase_refRaw
                : self::dbRowInt($fase_refRaw);
            $iAfecta = self::dbRowInt($row['afecta_a'] ?? 0);
            $perm_on = self::dbRowInt($row['perm_on'] ?? 0);
            $perm_off = self::dbRowInt($row['perm_off'] ?? 0);

            if (FuncTablasSupport::isTrue($dl_propia)) {
                if (array_key_exists($id_tipo_activ_txt, $this->aPermDl)) {
                    // machaco los valores existentes. Si he ordenado por id usuario (DESC), el último és el más importante.
                } else { //nuevo
                    $this->aPermDl[$id_tipo_activ_txt] = new XResto($id_tipo_activ_txt);
                }
            } else {
                if (array_key_exists($id_tipo_activ_txt, $this->aPermOtras)) {
                    // machaco los valores existentes. Si he ordenado por id usuario (DESC), el último és el más importante.
                } else { //nuevo
                    $this->aPermOtras[$id_tipo_activ_txt] = new XResto($id_tipo_activ_txt);
                }
            }
            if (FuncTablasSupport::isTrue($dl_propia)) {
                $this->aPermDl[$id_tipo_activ_txt]->setOmplir($iAfecta, $fase_ref, $perm_on, $perm_off);
            } else {
                $this->aPermOtras[$id_tipo_activ_txt]->setOmplir($iAfecta, $fase_ref, $perm_on, $perm_off);
            }

            if (!empty($id_tipo_activ_txt)) {
                if (!empty($this->aPermDl[$id_tipo_activ_txt])) {
                    $this->aPermDl[$id_tipo_activ_txt]->setOrdenar();
                }
                if (!empty($this->aPermOtras[$id_tipo_activ_txt])) {
                    $this->aPermOtras[$id_tipo_activ_txt]->setOrdenar();
                }
            }
        }

        return true;
    }

    /**
     * fija las propiedades de dl_propia y id_tipo_activ.
     *
     * Si se pasan $id_tipo_activ y $dl_org (como en controladores frontend que
     * ya cargaron la entidad), no se consulta el repositorio. Si solo se pasa
     * $id_activ, se resuelve vía {@see ActividadAllRepositoryInterface} o
     * contexto cacheado de una llamada previa con los tres datos.
     *
     * @param int $id_activ
     */
    public function setActividad(int $id_activ, ?string $id_tipo_activ = null, ?string $dl_org = null): void
    {
        if ($this->iid_activ !== $id_activ) {
            $this->aFasesCompletadas = [];
        }

        $this->btop = false;

        $tieneContexto = $id_tipo_activ !== null && $id_tipo_activ !== ''
            && $dl_org !== null && $dl_org !== '';

        if ($tieneContexto) {
            $this->iid_activ = $id_activ;
            $this->setActividadContextTipo = (string)$id_tipo_activ;
            $this->setActividadContextDlOrg = (string)$dl_org;
            $this->applyActividadTipoYDelegacion($this->setActividadContextTipo, $this->setActividadContextDlOrg);

            return;
        }

        if ($id_activ === $this->iid_activ
            && $this->setActividadContextTipo !== null
            && $this->setActividadContextDlOrg !== null) {
            $this->applyActividadTipoYDelegacion($this->setActividadContextTipo, $this->setActividadContextDlOrg);

            return;
        }

        $this->iid_activ = $id_activ;

        $oActividad = $this->getActividadAllRepository()->findById($id_activ);
        if ($oActividad === null) {
            throw new RuntimeException(
                sprintf('PermisosActividades::setActividad: actividad %d no encontrada.', $id_activ)
            );
        }
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $dl_org = $oActividad->getDl_org();

        $this->setActividadContextTipo = (string)$id_tipo_activ;
        $this->setActividadContextDlOrg = (string)$dl_org;
        $this->applyActividadTipoYDelegacion($this->setActividadContextTipo, $this->setActividadContextDlOrg);
    }

    private function applyActividadTipoYDelegacion(string $id_tipo_activ, string $dl_org): void
    {
        $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);

        $this->sid_tipo_activ = $id_tipo_activ;

        if ($dl_org === ConfigGlobal::mi_delef() || $dl_org_no_f === ConfigGlobal::mi_dele()) {
            $this->bpropia = true;
        } else {
            $this->bpropia = false;
        }
    }

    public function setId_fase(int $iid_fase): void
    {
        $this->iid_fase = $iid_fase;
    }

    public function getId_fase(): int
    {
        return $this->iid_fase;
    }

    /**
     * Resuelve fases completadas: o bien ya vienen en sesión (setFasesCompletadas),
     * o bien se cargan una sola vez con el repositorio inyectado.
     */
    private function ensureFasesCompletadasLoaded(): void
    {
        if (!empty($this->aFasesCompletadas)) {
            return;
        }
        if ($this->iid_activ === 0) {
            throw new RuntimeException(
                'PermisosActividades: sin fases en caché ni id_activ. '
                . 'En frontend use PostRequest a /src/actividades/actividad_fases_completadas_datos y setFasesCompletadas.'
            );
        }
        $fases = $this->getActividadProcesoTareaRepository()->getFasesCompletadas($this->iid_activ);
        $this->aFasesCompletadas = array_map(static fn ($id) => (int) $id, $fases);
    }

    private function isCompletada(int|string $id_fase): bool
    {
        if (empty($id_fase)) {
            exit (_("Hay que indicar para que fase"));
        }

        $this->ensureFasesCompletadasLoaded();
        $idFase = (int)$id_fase;
        $fasesNorm = array_map(static fn ($v) => (int)$v, $this->aFasesCompletadas);

        return \in_array($idFase, $fasesNorm, true);
    }

    /**
     * @param list<int> $aFases
     */
    public function setFasesCompletadas(array $aFases = []): void
    {
        $this->aFasesCompletadas = array_map(static fn ($id) => (int) $id, $aFases);
    }

    /**
     * Para saber si puedo crear una actividad del tipo para dl.
     *
     * @return array{of_responsable_txt: string, status: int}|false
     */
    public function getPermisoCrear(bool $dl_propia): array|false
    {
        if (!ConfigGlobal::is_app_installed('procesos')) {
            return ['of_responsable_txt' => '', 'status' => 0];
        }
        $this->bpropia = $dl_propia;
        $id_tipo_activ = $this->sid_tipo_activ;
        $aTiposDeProcesos = $this->getTipoDeActividadRepository()->getTiposDeProcesos($id_tipo_activ, $dl_propia);

        if (empty($aTiposDeProcesos)) {
            echo _("debería crear un proceso para este tipo de actividad");
            return false;
        }
        $oPerm = false;
        $of_responsable_txt = '';
        $status = 0;
        foreach ($aTiposDeProcesos as $id_tipo_proceso) {
            // Buscar la primera fase (no depende de fases previas)
            try {
                $aTareaProceso = $this->getTareaProcesoRepository()->getFaseIndependiente($id_tipo_proceso);
            } catch (\RuntimeException) {
                continue;
            }
            $oTareaProceso = $aTareaProceso[0];
            $of_responsable_txt = $oTareaProceso->getOf_responsable_txt();
            $status = $oTareaProceso->getStatus();

            // devolver false si no puedo crear
            $iAfecta = 1; //datos
            $id_fase_ref = FaseId::FASE_APROBADA;
            $on_off = 'off';

            $oP = $this->getPermisos($iAfecta);
            if ($oP === false) {
                return false;
            }
            $iperm = $oP->getPerm($iAfecta, $id_fase_ref, $on_off);
            if ($iperm !== 0) {
                $oPerm = new PermAccion($iperm);
                break;
            }
        }

        if ($oPerm !== false && $oPerm->have_perm_activ('crear')) {
            return [
                'of_responsable_txt' => $of_responsable_txt,
                'status' => $status,
            ];
        }

        return false;
    }

    /**
     * Devuelve el oPersonaNota PermAction para $sAfecta
     * Para la actividad $this->iidactiv y en la fase $this->id_fase
     *
     * @param string $sAfecta
     * @return PermAccion
     */
    public function getPermisoActual(string $sAfecta): PermAccion
    {
        // hay que poner a cero el id_tipo_activ, sino
        // aprovecha el que se ha buscado con el anterior iAfecta.
        if (!empty($this->iid_activ)) {
            $this->setActividad($this->iid_activ);
        }
        // para poder pasar el valor de afecta con texto:
        $iAfecta = self::AFECTA[$sAfecta];

        // buscar fase_ref para iAfecta
        $id_fase_ref = $this->getFaseRef($iAfecta);
        if ($this->btop || $id_fase_ref === false) {
            return new PermAccion(0);
        }
        $completada = $this->isCompletada($id_fase_ref);
        $on_off = FuncTablasSupport::isTrue($completada) ? 'on' : 'off';

        $oPerm = $this->resolveXRestoForTipoActividad();
        if ($oPerm === null) {
            return new PermAccion(0);
        }
        $perm = $oPerm->getPerm($iAfecta, $id_fase_ref, $on_off);

        return new PermAccion($perm !== 0 ? $perm : 0);
    }

    /**
     * Devuelve el oPersonaNota PermAction para $iAfecta
     * Para la actividad $this->iidactiv
     * que esté con la $this->id_fase en 'on'.
     *
     * @param integer|string $iAfecta
     * @return PermAccion
     */
    public function getPermisoOn(int|string $iAfecta): PermAccion
    {
        // hay que poner a cero el id_tipo_activ, sino
        // aprovecha el que se ha buscado con el anterior iAfecta.
        if (!empty($this->iid_activ)) {
            $this->setActividad($this->iid_activ);
        }
        // para poder pasar el valor de afecta con texto:
        if (is_string($iAfecta)) {
            $iAfecta = self::AFECTA[$iAfecta];
        }

        // buscar fase_ref para iAfecta
        $id_fase_ref = $this->getFaseRef($iAfecta);
        if ($this->btop || $id_fase_ref === false) {
            return new PermAccion(0);
        }
        $completada = $this->isCompletada($id_fase_ref);
        if (!FuncTablasSupport::isTrue($completada)) {
            return new PermAccion(0);
        }

        $oPerm = $this->resolveXRestoForTipoActividad();
        if ($oPerm === null) {
            return new PermAccion(0);
        }

        $perm = $oPerm->getPerm($iAfecta, $id_fase_ref, 'on');

        return new PermAccion($perm !== 0 ? $perm : 0);
    }

    private function resolveXRestoForTipoActividad(): ?XResto
    {
        if ($this->bpropia) {
            return $this->aPermDl[$this->sid_tipo_activ] ?? null;
        }

        return $this->aPermOtras[$this->sid_tipo_activ] ?? null;
    }

    /**
     * @return int|string|false
     */
    private function getFaseRef(int $iAfecta, string $id_tipo_activ_txt = ''): int|string|false
    {
        if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->sid_tipo_activ;
        $id_tipo_activ_txt = $this->completarId($id_tipo_activ_txt);
        if ($this->bpropia === true) {
            if (array_key_exists($id_tipo_activ_txt, $this->aPermDl)) {
                $PermIdTipo = $this->aPermDl[$id_tipo_activ_txt];
                // a ver si existe el iAfecta para este id_tipo_activ:
                if ($PermIdTipo->hasAfecta($iAfecta)) {
                    return $PermIdTipo->getFaseRef($iAfecta) ?? false;
                } else {
                    return $this->getFaseRefPrev($iAfecta, $id_tipo_activ_txt);
                }
            } else {
                return $this->getFaseRefPrev($iAfecta, $id_tipo_activ_txt);
            }
        } else {
            if (array_key_exists($id_tipo_activ_txt, $this->aPermOtras)) {
                $PermIdTipo = $this->aPermOtras[$id_tipo_activ_txt];
                // a ver si existe el iAfecta para este id_tipo_activ:
                if ($PermIdTipo->hasAfecta($iAfecta)) {
                    return $PermIdTipo->getFaseRef($iAfecta) ?? false;
                } else {
                    return $this->getFaseRefPrev($iAfecta, $id_tipo_activ_txt);
                }
            } else {
                return $this->getFaseRefPrev($iAfecta, $id_tipo_activ_txt);
            }
        }
    }

    /**
     * @return int|string|false
     */
    private function getFaseRefPrev(int $iAfecta, string $id_tipo_activ_txt = ''): int|string|false
    {
        if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->sid_tipo_activ;
        if (($prev_id_tipo = $this->getIdTipoPrev($id_tipo_activ_txt)) === false) {
            return false;
        }
        return $this->getFaseRef($iAfecta, $prev_id_tipo);
    }

    /**
     * para saber si un sacd puede ver una actividad, según sea el encargado, o asistente
     * o los dos.
     * los parámetros provienen de la consulta:
     * $cAsistentes = $oGesActividadCargo ->getAsistenteCargoDeActividad();
     *
     * @param ?integer $id_cargo
     * @param boolean $propio
     * @return boolean
     */
    public function havePermisoSacd(?int $id_cargo, bool $propio): bool
    {
        $permiso_ver = FALSE;
        $oPermActiv = $this->getPermisoActual('datos');
        // sólo si la fase de 'ok sacd' está completada:
        $oPermSacd = $this->getPermisoOn('sacd');
        // sólo si la fase de 'ok asist. sacd' está completada:
        $oPermAsisSacd = $this->getPermisoOn('asistentesSacd');
        // para ver la actividad:
        if ($oPermActiv->have_perm_activ('ver') === FALSE) {
            return FALSE;
            // No hace falta seguir mirando.
        }

        // si es solo cargo, tiene propio='f' como sacd de la actividad
        if (!empty($id_cargo)) {
            if ($oPermSacd->have_perm_activ('ver') === TRUE) {
                $permiso_ver = TRUE;
            }
            //si también asiste. tiene propio = 't'
            if (FuncTablasSupport::isTrue($propio) && $oPermAsisSacd->have_perm_activ('ver') === TRUE) {
                $permiso_ver = TRUE;
            }
        } else {
            // sólo asiste
            if ($oPermAsisSacd->have_perm_activ('ver') === TRUE) {
                $permiso_ver = TRUE;
            }
        }
        return $permiso_ver;
    }

    public function getPermisos(int $iAfecta, string $id_tipo_activ_txt = ''): XResto|false
    {
        if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->sid_tipo_activ;
        $id_tipo_activ_txt = $this->completarId($id_tipo_activ_txt);
        if ($this->bpropia === true) {
            if (array_key_exists($id_tipo_activ_txt, $this->aPermDl)) {
                $PermIdTipo = $this->aPermDl[$id_tipo_activ_txt];
                // a ver si existe el iAfecta para este id_tipo_activ:
                if ($PermIdTipo->hasAfecta($iAfecta)) {
                    return $this->aPermDl[$id_tipo_activ_txt];
                } else {
                    return $this->getPermisosPrev($iAfecta, $id_tipo_activ_txt);
                }
            } else {
                return $this->getPermisosPrev($iAfecta, $id_tipo_activ_txt);
            }
        } else {
            if (array_key_exists($id_tipo_activ_txt, $this->aPermOtras)) {
                return $this->aPermOtras[$id_tipo_activ_txt];
            } else {
                return $this->getPermisosPrev($iAfecta, $id_tipo_activ_txt);
            }
        }
    }

    public function getPermisosPrev(int $iAfecta, string $id_tipo_activ_txt = ''): XResto|false
    {
        if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->sid_tipo_activ;
        if (($prev_id_tipo = $this->getIdTipoPrev($id_tipo_activ_txt)) === false) {
            return false;
        }
        return $this->getPermisos($iAfecta, $prev_id_tipo);
    }

    /**
     * @return array<string, int>
     */
    public function getAfecta(): array
    {
        return self::AFECTA;
    }

    public function setId_tipo_activ(string $id_tipo_activ_txt): void
    {
        if ($id_tipo_activ_txt === '......') {
            $this->btop = true;
        } else {
            $this->btop = false;
        }
        // actualizar el id_tipo_activ
        $this->sid_tipo_activ = $id_tipo_activ_txt;
    }

    public function setId_activ(int $id_activ): void
    {
        $this->iid_activ = $id_activ;
    }

    public function setId_tipo_proceso(int $id_tipo_proceso): void
    {
        $this->iid_tipo_proceso = $id_tipo_proceso;
    }

    public function getId_tipo_proceso(): int
    {
        return $this->iid_tipo_proceso;
    }

    public function setPropia(bool|string $bpropia): void
    {
        // actualitza el bpropia
        if (FuncTablasSupport::isTrue($bpropia)) {
            $this->bpropia = true;
        } else {
            $this->bpropia = false;
        }
    }

    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /**
     * @return string|false
     */
    private function getIdTipoPrev(string $id_tipo_activ_txt = ''): string|false
    {
        if (empty($id_tipo_activ_txt)) $id_tipo_activ_txt = $this->sid_tipo_activ;
        $match = [];
        $rta = preg_match('/(\d+)(\d)(\.*)/', $id_tipo_activ_txt, $match);
        if (empty($rta)) {
            if ($id_tipo_activ_txt === '1.....' || $id_tipo_activ_txt === '2.....' || $id_tipo_activ_txt === '3.....') {
                $this->btop = true; // ja no puc pujar més amunt.
                return '......';
            } else {
                $this->btop = true; // ja no puc pujar més amunt.
                return false;
            }
        }

        $num_prev = $match[1];
        $num = $match[2];
        $pto = $match[3];

        $prev_id_tipo = $num_prev . "." . $pto;
        //echo "<br>$num, $num_prev, $prev_id_tipo <br>";
        //print_r($this);
        $this->sid_tipo_activ = $prev_id_tipo;
        return $prev_id_tipo;
    }

    private function completarId(string $id_tipo_activ_txt): string
    {
        $len = strlen($id_tipo_activ_txt);
        if ($len < 6) {
            $relleno = 6 - $len;
            for ($i = 0; $i < $relleno; $i++) {
                $id_tipo_activ_txt .= '.';
            }
        }
        return $id_tipo_activ_txt;
    }

    private static function dbRowString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (string) $value;
        }

        return '';
    }

    private static function dbRowInt(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }

        return 0;
    }

    /**
     * @return array<string, XResto>
     */
    private static function restoreXRestoMap(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }
        $out = [];
        foreach ($value as $key => $item) {
            if (is_string($key) && $item instanceof XResto) {
                $out[$key] = $item;
            }
        }

        return $out;
    }

    /**
     * @return list<int>
     */
    private static function restoreIntList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_map(static fn ($id) => self::dbRowInt($id), $value));
    }

    private static function optionalString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return self::dbRowString($value);
    }
}
