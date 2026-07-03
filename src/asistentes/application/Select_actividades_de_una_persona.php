<?php

namespace src\asistentes\application;

use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\permisos\domain\XPermisos;
use src\shared\config\ConfigGlobal;
use src\dossiers\application\PermDossier;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\asistentes\application\services\AsistenteActividadService;
use src\dossiers\application\DossierTipoPublicUrls;
use src\personas\domain\entity\Persona;
use src\actividades\domain\entity\TiposActividades;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Widget del dossier `1301` (codigo `actividades_de_una_persona`):
 * actividades a las que asiste una persona.
 *
 * El HTML lo renderiza {@see \frontend\asistentes\helpers\SelectActividadesDeUnaPersonaRender}
 * desde {@see self::getSegmentData()} (sin `frontend\` en `src/`).
 *
 * Sucesor de `apps/asistentes/model/Select1301.php`. Instanciado dinamicamente por
 * {@see \src\dossiers\application\DossierTipoFileSuffixResolver::resolveSelectClassFqcn()}.
 */
class Select_actividades_de_una_persona
{
    private const ID_TIPO_DOSSIER = 1301;

    public function __construct(
        private AsistenteActividadService $asistenteActividadService,
        private ActividadAllRepositoryInterface $actividadAllRepository,
    ) {
    }

    /** @var array<string, array{perm: mixed, nom: mixed}> */
    private array $ref_perm = [];
    private string $msg_err = '';

    /** @var array<int|string, mixed> */
    private array $a_valores = [];

    private string $txt_eliminar = '';
    private string $bloque = '';

    private string $queSel = '';

    /** @var int|string */
    private $id_dossier;

    private string $pau = '';
    private string $obj_pau = '';
    private int $id_pau = 0;
    private int $permiso = 0;
    private int $modo_curso = 1;

    /** @var int|string|null */
    private $Qid_sel;

    /** @var int|string|null */
    private $Qscroll_id;

    // Clave actual de la pila de navegación, inyectada desde el controller frontend.
    private int $stackActual = 0;

    /** @var array<string, array{path: string, query: array<string, mixed>}> */
    private array $aLinks_dl = [];

    /** @var array<string, array{path: string, query: array<string, mixed>}> */
    private array $aLinks_otros = [];

    private mixed $status;

    /** @return array{0: array<string, mixed>, 1: array<string, string>} */
    private function cursoWhereFromModo(int $modo_curso): array
    {
        $mes = date('m');
        /** @var ConfigSnapshot $oConfig */
        $oConfig = $_SESSION['oConfig'];
        $fin_m = $oConfig->getMesFinStgr();
        $any = ($mes > $fin_m) ? (int) date('Y') + 1 : date('Y');
        $inicurs_ca = FuncTablasSupport::cursoEst("inicio", $any)->format('Y-m-d');
        $fincurs_ca = FuncTablasSupport::cursoEst("fin", $any)->format('Y-m-d');

        /** @var array<string, mixed> $aWhere */
        $aWhere = ['_ordre' => 'f_ini'];
        /** @var array<string, string> $aOperator */
        $aOperator = [];
        switch ($modo_curso) {
            case 2:
                $aWhere['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
                $aOperator['f_ini'] = 'BETWEEN';
                break;
            case 3:
                break;
            case 1:
            default:
                $aWhere['status'] = StatusId::ACTUAL;
                $aWhere['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
                $aOperator['f_ini'] = 'BETWEEN';
                break;
        }

        return [$aWhere, $aOperator];
    }

    /** @return array<int, array{txt: string, click: string}> */
    private function getBotones(): array
    {
        return [
            ['txt' => _("modificar asistencia"), 'click' => "fnjs_modificar(this.form)"],
            ['txt' => _("borrar asistencia"), 'click' => "fnjs_borrar(this.form)"],
        ];
    }

    /** @return array<int, string|array{name: string, width: int}> */
    private function getCabeceras(): array
    {
        return [
            ['name' => _("fechas"), 'width' => 150],
            ['name' => _("nombre"), 'width' => 300],
            _("propio"),
            _("est. ok"),
            _("falta"),
            _("observ."),
        ];
    }

    /** @return array<int|string, mixed> */
    private function getValores(): array
    {
        if (empty($this->a_valores) && $this->msg_err === '') {
            $this->getTabla();
        }

        return $this->a_valores;
    }

    private function getTabla(): void
    {
        $mi_sfsv = ConfigGlobal::mi_sfsv();

        /** @var XPermisos $oPerm */
        $oPerm = $_SESSION['oPerm'];

        [$aWhere, $aOperator] = $this->cursoWhereFromModo($this->modo_curso);

        $oPersona = Persona::findPersonaEnGlobal($this->id_pau);
        if (!is_object($oPersona)) {
            $this->msg_err = "<br>No encuentro a ninguna persona con id_nom: {$this->id_pau} en  " . __FILE__ . ": line " . __LINE__;
            $this->a_valores = [];
            $this->ref_perm = [];

            return;
        }
        $id_tabla = $oPersona->getId_tabla();
        $oPermDossier = new PermDossier();
        $rawRefPerm = $oPermDossier->perm_activ_pers($id_tabla);
        $this->ref_perm = [];
        foreach ($rawRefPerm as $key => $value) {
            $this->ref_perm[(string) $key] = [
                'perm' => $value['perm'] ?? null,
                'nom' => $value['nom'] ?? null,
            ];
        }

        $i = 0;
        $a_valores = [];
        $aWhereNom = ['id_nom' => $this->id_pau];
        $aOperadorNom = [];
        $cActividadesAsistente = $this->asistenteActividadService->getActividadesDeAsistente(
            $aWhereNom,
            $aOperadorNom,
            $aWhere,
            $aOperator,
            true,
        );
        foreach ($cActividadesAsistente as $oAsistente) {
            $i++;
            $id_activ = $oAsistente->getId_activ();
            $oActividad = $this->actividadAllRepository->findById($id_activ);
            if ($oActividad === null) {
                continue;
            }
            $nom_activ = $oActividad->getNom_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $f_ini = $oActividad->getF_ini()?->getFromLocal();
            $f_fin = $oActividad->getF_fin()?->getFromLocal();

            $propio = $oAsistente->isPropio();
            $falta = $oAsistente->isFalta();
            $est_ok = $oAsistente->isEst_ok();
            $observ = $oAsistente->getObserv();

            $oTipoActividad = new TiposActividades($id_tipo_activ);
            $isfsv = $oTipoActividad->getSfsvId();
            if ($mi_sfsv != $isfsv && !$oPerm->have_perm_oficina('des')) {
                $ssfsv = $oTipoActividad->getSfsvText();
                $sactividad = $oTipoActividad->getActividadText();
                $nom_activ = "$ssfsv $sactividad";
            }
            $id_tipo = substr((string) $id_tipo_activ, 0, 3);
            $act = $this->ref_perm[$id_tipo] ?? [];
            $permiso = !empty($act['perm']) ? 3 : 1;

            FuncTablasSupport::isTrue($propio) ? $chk_propio = "si" : $chk_propio = "no";
            FuncTablasSupport::isTrue($falta) ? $chk_falta = "si" : $chk_falta = "no";
            FuncTablasSupport::isTrue($est_ok) ? $chk_est_ok = "si" : $chk_est_ok = "no";

            $a_valores[$i]['sel'] = $permiso == 3 ? "$id_activ" : "";
            $a_valores[$i][1] = "$f_ini-$f_fin";
            $a_valores[$i][2] = $nom_activ;
            $a_valores[$i][3] = $chk_propio;
            $a_valores[$i][4] = $chk_est_ok;
            $a_valores[$i][5] = $chk_falta;
            $a_valores[$i][6] = $observ;
        }
        if (!empty($a_valores)) {
            if (!empty($this->Qid_sel)) {
                $a_valores['select'] = $this->Qid_sel;
            }
            if (!empty($this->Qscroll_id)) {
                $a_valores['scroll_id'] = $this->Qscroll_id;
            }
        }

        $this->a_valores = $a_valores;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSegmentData(): array
    {
        $this->txt_eliminar = _("¿Está seguro que desea borrar a esta persona de esta actividad?");
        $this->getValores();
        $this->setLinksInsert();

        return [
            'segment_tipo' => 'select_actividades_de_una_persona',
            'modo_curso' => $this->modo_curso,
            'msg_err' => $this->msg_err,
            'wrapper' => [
                'txt_eliminar' => $this->txt_eliminar,
                'bloque' => $this->bloque,
                'url_form_relative' => DossierTipoPublicUrls::relativeFormController(self::ID_TIPO_DOSSIER),
                'url_eliminar_path' => 'src/asistentes/asistente_eliminar',
            ],
            'hash' => [
                'campos_form' => 'modo_curso',
                'campos_no' => 'sel!mod!scroll_id!refresh!id_sel',
                'campos_hidden' => [
                    'pau' => $this->pau,
                    'id_pau' => $this->id_pau,
                    'obj_pau' => $this->obj_pau,
                    'queSel' => $this->queSel,
                    'id_dossier' => $this->id_dossier,
                    'permiso' => 3,
                    'stack' => $this->stackActual,
                ],
            ],
            'tabla' => [
                'id_tabla' => 'select_actividades_de_una_persona',
                'cabeceras' => $this->getCabeceras(),
                'botones' => $this->getBotones(),
                'valores' => $this->getValores(),
            ],
            'links_dl_specs' => $this->aLinks_dl,
            'links_otros_specs' => $this->aLinks_otros,
        ];
    }

    private function setLinksInsert(): void
    {
        $this->aLinks_dl = [];
        $this->aLinks_otros = [];
        $ref_perm = $this->ref_perm;
        if (empty($ref_perm)) {
            return;
        }
        $mi_dele = ConfigGlobal::mi_delef();
        reset($ref_perm);
        foreach ($ref_perm as $clave => $val) {
            if (empty($val["perm"])) {
                continue;
            }
            $nomTxt = $val['nom'] ?? '';
            $nom = is_scalar($nomTxt) ? (string) $nomTxt : '';
            $aQuery = [
                'mod' => 'nuevo',
                'que_dl' => $mi_dele,
                'pau' => $this->pau,
                'id_tipo' => $clave,
                'obj_pau' => $this->obj_pau,
                'id_dossier' => $this->id_dossier,
                'id_pau' => $this->id_pau,
            ];
            $this->aLinks_dl[$nom] = DossierTipoPublicUrls::formControllerLinkSpec(self::ID_TIPO_DOSSIER, $aQuery);
        }
        reset($ref_perm);
        foreach ($ref_perm as $clave => $val) {
            if (empty($val['perm'])) {
                continue;
            }
            $nomTxt = $val['nom'] ?? '';
            $nom = is_scalar($nomTxt) ? (string) $nomTxt : '';
            $aQuery = [
                'mod' => 'nuevo',
                'pau' => $this->pau,
                'id_tipo' => $clave,
                'obj_pau' => $this->obj_pau,
                'id_dossier' => $this->id_dossier,
                'id_pau' => $this->id_pau,
            ];
            $this->aLinks_otros[$nom] = DossierTipoPublicUrls::formControllerLinkSpec(self::ID_TIPO_DOSSIER, $aQuery);
        }
    }

    public function setModo_curso(string|int|float|bool|null $modo_curso = null): void
    {
        $modo = (int) ($modo_curso ?? 0);
        $this->modo_curso = $modo === 0 ? 1 : $modo;
    }

    public function getId_dossier(): int|string { return $this->id_dossier; }
    public function getPau(): string { return $this->pau; }
    public function getObj_pau(): string { return $this->obj_pau; }
    public function getId_pau(): int { return $this->id_pau; }
    public function getPermiso(): int { return $this->permiso; }
    public function getStatus(): mixed { return $this->status; }

    public function setId_dossier(int|string $Qid_dossier): void { $this->id_dossier = $Qid_dossier; }
    public function setPau(string|int|float|bool|null $Qpau = null): void { $this->pau = $Qpau === null ? '' : (string) $Qpau; }
    public function setObj_pau(string|int|float|bool|null $Qobj_pau = null): void { $this->obj_pau = $Qobj_pau === null ? '' : (string) $Qobj_pau; }
    public function setId_pau(string|int|float|bool|null $Qid_pau = null): void { $this->id_pau = (int) ($Qid_pau ?? 0); }
    public function setPermiso(string|int|float|bool|null $Qpermiso = null): void { $this->permiso = (int) ($Qpermiso ?? 0); }
    public function setStatus(mixed $Qstatus): void { $this->status = $Qstatus; }
    public function setQid_sel(int|string|null $Qid_sel): void { $this->Qid_sel = $Qid_sel; }
    public function setQscroll_id(int|string|null $Qscroll_id): void { $this->Qscroll_id = $Qscroll_id; }
    public function setBloque(string|int|float|bool|null $bloque = null): void { $this->bloque = $bloque === null ? '' : (string) $bloque; }
    public function setQueSel(string|int|float|bool|null $queSel = null): void { $this->queSel = $queSel === null ? '' : (string) $queSel; }
    public function setStackActual(int $stack): void { $this->stackActual = $stack; }
}
