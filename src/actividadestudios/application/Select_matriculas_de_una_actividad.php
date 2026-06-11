<?php

namespace src\actividadestudios\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\dossiers\application\DossierTipoPublicUrls;
use src\personas\domain\entity\Persona;

/**
 * Widget del dossier `3103` (codigo `matriculas_de_una_actividad`):
 * listado de matriculas de una actividad, agrupadas por asignatura.
 *
 * El HTML lo renderiza {@see \frontend\actividadestudios\helpers\SelectMatriculasDeUnaActividadRender}
 * a partir de {@see self::getSegmentData()} (sin dependencias `frontend\` en `src/`).
 *
 * Sucesor de `apps/actividadestudios/model/Select3103.php`. Instanciado
 * dinamicamente por
 * {@see \src\dossiers\application\DossierTipoFileSuffixResolver::resolveSelectClassFqcn()}.
 */
class Select_matriculas_de_una_actividad
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private ActividadAsignaturaDlRepositoryInterface $actividadAsignaturaDlRepository,
        private ActividadAsignaturaRepositoryInterface $actividadAsignaturaRepository,
        private AsignaturaRepositoryInterface $asignaturaRepository,
        private MatriculaDlRepositoryInterface $matriculaDlRepository,
    ) {
    }

    private string $nom_activ = '';
    /** @var array<int|string, mixed> */
    private array $a_valores = [];
    /** @var array<int|string, string> */
    private array $a_grupos = [];
    private string $sin_asignaturas_mensaje = '';
    private string $msg_err = '';
    private string $bloque = '';

    private string $queSel = '';
    private int $id_dossier = 3103;
    private string $pau = '';
    private string $obj_pau = '';
    private int $id_pau = 0;
    private int $permiso = 1;

    /** @var int|string|null */
    private $Qid_sel;
    /** @var int|string|null */
    private $Qscroll_id;

    /** @return array<int, array{txt: string, click: string}> */
    public function getBotones(): array
    {
        return [
            ['txt' => _("borrar matrícula"), 'click' => 'fnjs_borrar(this.form)'],
        ];
    }

    /** @return array<int, string> */
    public function getCabeceras(): array
    {
        return [
            _("asignatura"),
            _("alumno"),
        ];
    }

    /** @return array<int|string, mixed> */
    public function getValores(): array
    {
        if (empty($this->a_valores) && $this->sin_asignaturas_mensaje === '') {
            $this->getTabla();
        }
        return $this->a_valores;
    }

    /** @return array<int|string, string> */
    public function getGrupos(): array
    {
        if (empty($this->a_grupos) && $this->sin_asignaturas_mensaje === '') {
            $this->getTabla();
        }
        return $this->a_grupos;
    }

    private function getTabla(): void
    {
        $this->sin_asignaturas_mensaje = '';
        $this->msg_err = '';
        $mi_dele = ConfigGlobal::mi_delef();
        $oActividad = $this->actividadAllRepository->findById($this->id_pau);
        if ($oActividad === null) {
            $this->sin_asignaturas_mensaje = _("no encuentro la actividad");
            return;
        }
        $this->nom_activ = $oActividad->getNom_activ();
        $dl_org = $oActividad->getDl_org();

        if ($mi_dele == $dl_org) {
            $this->permiso = 3;
            $repoAsignaturas = $this->actividadAsignaturaDlRepository;
        } else {
            $this->permiso = 1;
            $repoAsignaturas = $this->actividadAsignaturaRepository;
        }
        $cActividadAsignaturas = $repoAsignaturas->getActividadAsignaturas([
            'id_activ' => $this->id_pau,
            '_ordre' => 'id_asignatura',
        ]);

        if (count($cActividadAsignaturas) === 0) {
            $this->sin_asignaturas_mensaje = _("esta actividad no tiene ninguna asignatura");
            $this->a_valores = [];
            $this->a_grupos = [];

            return;
        }

        $a = 0;
        $msg_err = '';
        $aGrupos = [];
        $a_valores = [];
        foreach ($cActividadAsignaturas as $oActividadAsignatura) {
            $a++;
            $id_asignatura = $oActividadAsignatura->getId_asignatura();
            $id_profesor = $oActividadAsignatura->getId_profesor();

            if (!empty($id_profesor)) {
                $oPersona = Persona::findPersonaEnGlobal($id_profesor);
                if ($oPersona === null) {
                    $msg_err .= "<br>No encuentro a nadie con id_nom: $id_profesor (profesor) en  " . __FILE__ . ": line " . __LINE__;
                }
            }

            $oAsignatura = $this->asignaturaRepository->findById($id_asignatura);
            if ($oAsignatura === null) {
                throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
            }
            $nombre_corto = $oAsignatura->getNombre_corto() ?? '';
            $aGrupos[$id_asignatura] = $nombre_corto;

            $cMatriculas = $this->matriculaDlRepository->getMatriculas([
                'id_activ' => $this->id_pau,
                'id_asignatura' => $id_asignatura,
            ]);
            $m = 0;
            foreach ($cMatriculas as $oMatricula) {
                $id_nom = $oMatricula->getId_nom();
                $oPersona = Persona::findPersonaEnGlobal($id_nom);
                if ($oPersona === null) {
                    $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
                    continue;
                }
                $nom_persona = $oPersona->getPrefApellidosNombre();
                $ctr = $oPersona->getCentro_o_dl();

                $a_valores[$id_asignatura][$m]['sel'] = "$id_nom#$id_asignatura";
                $a_valores[$id_asignatura][$m][1] = $nombre_corto;
                $a_valores[$id_asignatura][$m][2] = "$nom_persona ($ctr)";
                $m++;
            }
        }
        if (!empty($a_valores)) {
            if (!empty($this->Qid_sel)) {
                $a_valores['select'] = $this->Qid_sel;
            }
            if (!empty($this->Qscroll_id)) {
                $a_valores['scroll_id'] = $this->Qscroll_id;
            }
        }

        $this->msg_err = $msg_err;
        $this->a_valores = $a_valores;
        $this->a_grupos = $aGrupos;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSegmentData(): array
    {
        $this->getTabla();

        return [
            'segment_tipo' => 'select_matriculas_de_una_actividad',
            'sin_asignaturas_mensaje' => $this->sin_asignaturas_mensaje,
            'msg_err' => $this->msg_err,
            'wrapper' => [
                'txt_eliminar' => _("¿Está seguro que desea quitar esta matrícula?"),
                'nom_activ' => $this->nom_activ,
                'bloque' => $this->bloque,
                'url_form_relative' => DossierTipoPublicUrls::relativeFormController(1303),
                'url_matricula_eliminar_path' => 'src/actividadestudios/matricula_eliminar',
            ],
            'hash' => [
                'campos_form' => '',
                'campos_no' => 'sel!mod!scroll_id!refresh!id_sel',
                'campos_hidden' => [
                    'pau' => $this->pau,
                    'id_pau' => $this->id_pau,
                    'obj_pau' => $this->obj_pau,
                    'queSel' => $this->queSel,
                    'id_dossier' => $this->id_dossier,
                    'permiso' => $this->permiso,
                    'bloque' => $this->bloque,
                ],
            ],
            'tabla' => [
                'grupos' => $this->a_grupos,
                'cabeceras' => $this->getCabeceras(),
                'botones' => $this->getBotones(),
                'valores' => $this->a_valores,
            ],
        ];
    }

    public function getId_dossier(): int { return $this->id_dossier; }
    public function getPau(): string { return $this->pau; }
    public function getObj_pau(): string { return $this->obj_pau; }
    public function getId_pau(): int { return $this->id_pau; }
    public function getPermiso(): int { return $this->permiso; }

    public function setId_dossier(int|string $id_dossier): void { $this->id_dossier = (int) $id_dossier; }
    public function setPau(string|int|float|bool|null $pau = null): void { $this->pau = $pau === null ? '' : (string) $pau; }
    public function setObj_pau(string|int|float|bool|null $obj_pau = null): void { $this->obj_pau = $obj_pau === null ? '' : (string) $obj_pau; }
    public function setId_pau(int|string $id_pau): void { $this->id_pau = (int) $id_pau; }
    public function setPermiso(int|string $permiso): void { $this->permiso = (int) $permiso; }
    public function setQid_sel(int|string|null $Qid_sel): void { $this->Qid_sel = $Qid_sel; }
    public function setQscroll_id(int|string|null $Qscroll_id): void { $this->Qscroll_id = $Qscroll_id; }
    public function setBloque(string|int|float|bool|null $bloque = null): void { $this->bloque = $bloque === null ? '' : (string) $bloque; }
    public function setQueSel(string|int|float|bool|null $queSel = null): void { $this->queSel = $queSel === null ? '' : (string) $queSel; }
}
