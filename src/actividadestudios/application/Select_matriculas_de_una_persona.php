<?php

namespace src\actividadestudios\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\dossiers\application\DossierTipoPublicUrls;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\entity\Persona;
use function src\shared\domain\helpers\curso_est;
use function src\shared\domain\helpers\is_true;

/**
 * Widget del dossier `1303` (codigo `matriculas_de_una_persona`):
 * asignaturas que cursa una persona agrupadas por actividad de estudios.
 *
 * El HTML lo renderiza {@see \frontend\actividadestudios\helpers\SelectMatriculasDeUnaPersonaRender}
 * a partir de {@see self::getSegmentData()} (sin dependencias `frontend\` en `src/`).
 *
 * Sucesor de `apps/actividadestudios/model/Select1303.php`. Instanciado
 * dinamicamente por
 * {@see \src\dossiers\application\DossierTipoFileSuffixResolver::resolveSelectClassFqcn()}.
 */
class Select_matriculas_de_una_persona
{
    private string $bloque = '';

    private string $queSel = '';
    private int $id_dossier = 1303;
    private string $pau = '';
    private string $obj_pau = '';
    private int $id_pau = 0;
    private int $permiso = 1;

    private $Qid_sel;
    private $Qscroll_id;

    /** @var mixed */
    private $todos;

    private $Qid_activ;

    private $cAsistencias;

    private $status;
    /** @var list<string> */
    private array $avisoLines = [];
    /** @var array{dossiers_form_action: string, hash: array{campos_form: string, campos_no: string, campos_hidden: array<string, mixed>}, mensaje: string}|null */
    private ?array $avisoTodosForm = null;
    private mixed $id_activ;
    /** @var array{path: string, query: array<string, mixed>}|null */
    private ?array $linkAddSpec = null;

    public function getBotones($ca_num = 1)
    {
        if ($this->permiso === 3) {
            return [
                ['txt' => _("modificar"), 'click' => "fnjs_modificar(this.form,$ca_num)"],
                ['txt' => _("borrar matrícula"), 'click' => "fnjs_borrar(this.form,$ca_num)"],
            ];
        }

        return [];
    }

    public function getCabeceras(): array
    {
        return [
            _("preceptor"),
            _("asignatura"),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCaPayload($oAsistente, int $ca_num): array
    {
        $htmlPrefix = '';
        $this->id_activ = $oAsistente->getId_activ();
        $propio = $oAsistente->isPropio();
        if (!is_true($propio)) {
            $htmlPrefix .= _("no está como propio, no debería tener plan de estudios");
        }

        $est_ok = $oAsistente->isEst_ok();
        $observ_est = $oAsistente->getObserv_est();
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $oActividad = $ActividadAllRepository->findById($this->id_activ);
        $nom_activ = $oActividad->getNom_activ();

        $oAlumno = Persona::findPersonaEnGlobal($this->id_pau);
        if ($oAlumno === null) {
            throw new \Exception(sprintf(_("No se ha encontrado alumno con id_nom: %s"), $this->id_pau));
        }
        $dl_alumno = $oAlumno->getDl();
        $classname = str_replace("personas\\model\\entity\\", '', get_class($oAlumno));
        $this->permiso = 3;
        if ($classname === 'PersonaEx') {
            $this->permiso = 3;
        } elseif ($dl_alumno != ConfigGlobal::mi_delef()) {
            $this->permiso = 2;
        }

        $MatriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
        $cMatriculas = $MatriculaRepository->getMatriculas([
            'id_nom' => $this->id_pau,
            'id_activ' => $this->id_activ,
            '_ordre' => 'id_nivel',
        ]);

        $form = "seleccionados" . $ca_num;
        if (is_true($est_ok)) {
            $chk_1 = "checked";
            $chk_2 = "";
        } else {
            $chk_1 = "";
            $chk_2 = "checked";
        }

        $i = 0;
        $a_valores = [];
        $msg_err = '';
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        foreach ($cMatriculas as $oMatricula) {
            $i++;
            $id_asignatura = $oMatricula->getId_asignatura();
            $preceptor = $oMatricula->isPreceptor();
            $id_preceptor = $oMatricula->getId_preceptor();
            if (is_true($preceptor)) {
                if (!empty($id_preceptor)) {
                    $oPersona = Persona::findPersonaEnGlobal($id_preceptor);
                    if (!is_object($oPersona)) {
                        $msg_err .= "<br>No encuentro a nadie con id_nom: $id_preceptor (profesor) en  " . __FILE__ . ": line " . __LINE__;
                        $preceptor = 'x';
                    } else {
                        $preceptor = $oPersona->getPrefApellidosNombre();
                    }
                } else {
                    $preceptor = _("por determinar");
                }
            } else {
                $preceptor = "";
            }

            $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
            if ($oAsignatura === null) {
                throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
            }
            $nombre_corto = $oAsignatura->getNombre_corto();

            $a_valores[$i]['sel'] = "$this->id_activ#$id_asignatura";
            $a_valores[$i][1] = $preceptor;
            $a_valores[$i][2] = $nombre_corto;
        }

        $this->setLinksInsert();

        if (!empty($msg_err)) {
            $htmlPrefix .= $msg_err;
        }

        return [
            'html_prefix' => $htmlPrefix,
            'hash' => [
                'campos_form' => 'est_ok!observ_est',
                'campos_no' => 'sel!mod!scroll_id!refresh',
                'campos_hidden' => [
                    'pau' => $this->pau,
                    'id_pau' => $this->id_pau,
                    'id_activ' => $this->id_activ,
                    'obj_pau' => $this->obj_pau,
                    'queSel' => $this->queSel,
                    'id_dossier' => $this->id_dossier,
                    'permiso' => $this->permiso,
                ],
            ],
            'tabla' => [
                'id_tabla' => 'sql_1303' . $ca_num,
                'cabeceras' => $this->getCabeceras(),
                'botones' => $this->getBotones($ca_num),
                'valores' => $a_valores,
            ],
            'link_add_spec' => $this->linkAddSpec,
            'nom_activ' => $nom_activ,
            'form' => $form,
            'ca_num' => $ca_num,
            'chk_1' => $chk_1,
            'chk_2' => $chk_2,
            'observ_est' => $observ_est,
            'permiso' => $this->permiso,
        ];
    }

    public function setLinksInsert(): void
    {
        $a_dataUrl = [
            'mod' => 'nuevo',
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'id_activ' => $this->id_activ,
        ];
        array_walk($a_dataUrl, 'src\\shared\\domain\\helpers\\poner_empty_on_null');
        $this->linkAddSpec = DossierTipoPublicUrls::formControllerLinkSpec($this->id_dossier, $a_dataUrl);
    }

    public function getAsistencias()
    {
        $this->avisoLines = [];
        $this->avisoTodosForm = null;

        $mes = date('m');
        $fin_m = $_SESSION['oConfig']->getMesFinStgr();
        if ($mes > $fin_m) {
            $any = (int) date('Y') + 1;
        } else {
            $any = date('Y');
        }
        $inicurs_ca = curso_est("inicio", $any)->format('Y-m-d');
        $fincurs_ca = curso_est("fin", $any)->format('Y-m-d');

        if ($this->id_pau < 0) {
            $PersonaDlRepository = $GLOBALS['container']->get(PersonaExRepositoryInterface::class);
        } else {
            $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        }
        $oPersona = $PersonaDlRepository->findById($this->id_pau);
        if ($oPersona === null) {
            throw new \Exception(sprintf(_("No se ha encontrado alumno con id_nom: %s"), $this->id_pau));
        }
        $stgr = $oPersona->getNivel_stgr();
        if ($stgr === 'r') {
            $this->avisoLines[] = _("está de repaso") . "<br>";
        }

        $aWhere = [];
        $aOperadores = [];
        $service = $GLOBALS['container']->get(AsistenteActividadService::class);
        if (!empty($this->Qid_activ)) {
            $aWhere['id_activ'] = $this->Qid_activ;
            $aWhereNom = ['id_nom' => $this->id_pau, 'id_activ' => $this->Qid_activ];
            $cAsistencias = $service->getActividadesDeAsistente($aWhereNom, [], $aWhere, $aOperadores, true);
        } else {
            if (empty($this->todos)) {
                $aWhere['f_ini'] = "'$inicurs_ca','$fincurs_ca'";
                $aOperadores['f_ini'] = 'BETWEEN';
            }
            $aWhere['id_tipo_activ'] = '^' . ConfigGlobal::mi_sfsv() . '(122)|(222)|(332)|(123)';
            $aOperadores['id_tipo_activ'] = '~';

            $aWhereNom = ['id_nom' => $this->id_pau, 'propio' => 't'];
            $cAsistencias = $service->getActividadesDeAsistente($aWhereNom, [], $aWhere, $aOperadores, true);
        }
        if (is_array($cAsistencias)) {
            $n = count($cAsistencias);
            if ($n === 0 && empty($this->todos)) {
                $this->avisoTodosForm = [
                    'dossiers_form_action' => 'frontend/dossiers/controller/dossiers_ver.php',
                    'hash' => [
                        'campos_form' => '',
                        'campos_no' => 'scroll_id',
                        'campos_hidden' => [
                            'pau' => 'p',
                            'id_pau' => $this->id_pau,
                            'obj_pau' => $this->obj_pau,
                            'id_dossier' => 1303,
                            'permiso' => '3',
                            'que' => 'matriculas',
                            'todos' => 1,
                            'mod' => 'xx',
                        ],
                    ],
                    'mensaje' => _(sprintf(_("No tiene asignado ningún ca como propio este curso: %s - %s."), $inicurs_ca, $fincurs_ca)),
                ];
            }

            if ($n === 0 && !empty($this->todos)) {
                $this->avisoLines[] = _("no tiene asignado ningún ca.");
            }
            if ($n > 1 && empty($this->todos)) {
                $nn = 0;
                $id_sem_inv = (int) ConfigGlobal::mi_sfsv() . '32500';
                $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
                foreach ($cAsistencias as $oAsistente) {
                    $oActividad = $ActividadAllRepository->findById($oAsistente->getId_activ());
                    if ($oActividad->getId_tipo_activ() != $id_sem_inv) {
                        $nn++;
                    }
                }
                if ($nn > 1) {
                    $this->avisoLines[] = _(sprintf(_("¡¡ojo!! tiene %s actividades de estudios asignadas como propias."), $n));
                }
            }
        }
        $this->cAsistencias = is_array($cAsistencias ?? null) ? $cAsistencias : [];

        return $this->cAsistencias;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSegmentData(): array
    {
        $this->getAsistencias();
        $cas = [];
        $ca_num = 0;
        foreach ($this->cAsistencias as $oAsistente) {
            $ca_num++;
            $cas[] = $this->buildCaPayload($oAsistente, $ca_num);
        }

        return [
            'segment_tipo' => 'select_matriculas_de_una_persona',
            'wrapper' => [
                'txt_eliminar' => _("¿Está seguro que desea quitar esta asignatura de esta actividad?"),
                'bloque' => $this->bloque,
                'url_form_relative' => DossierTipoPublicUrls::relativeFormController($this->id_dossier),
                'url_matricular_path' => 'src/actividadestudios/matricula_automatica',
                'url_matricula_eliminar_path' => 'src/actividadestudios/matricula_eliminar',
                'url_asistente_observ_est_path' => 'src/actividadestudios/asistente_observ_est',
                'url_asistente_plan_est_ok_path' => 'src/actividadestudios/asistente_plan_est_ok',
            ],
            'aviso_lines' => $this->avisoLines,
            'aviso_todos_form' => $this->avisoTodosForm,
            'cas' => $cas,
            'empty_cas_message' => count($this->cAsistencias) === 0
                ? _("no tiene ninguna actividad asignada. O no es de mi dl")
                : '',
        ];
    }

    public function setTodos(mixed $todos): void
    {
        $this->todos = $todos;
    }

    public function getId_dossier() { return $this->id_dossier; }
    public function getPau(): string { return $this->pau; }
    public function getObj_pau(): string { return $this->obj_pau; }
    public function getId_pau(): int { return $this->id_pau; }
    public function getPermiso(): int { return $this->permiso; }
    public function getStatus() { return $this->status; }

    public function setId_dossier($id_dossier): void { $this->id_dossier = (int) $id_dossier; }
    public function setPau($pau): void { $this->pau = (string) $pau; }
    public function setObj_pau($obj_pau): void { $this->obj_pau = (string) $obj_pau; }
    public function setId_pau($id_pau): void { $this->id_pau = (int) $id_pau; }
    public function setPermiso($permiso): void { $this->permiso = (int) $permiso; }
    public function setStatus($status): void { $this->status = $status; }
    public function setQid_sel($Qid_sel): void { $this->Qid_sel = $Qid_sel; }
    public function setQscroll_id($Qscroll_id): void { $this->Qscroll_id = $Qscroll_id; }
    public function setBloque($bloque): void { $this->bloque = (string) $bloque; }
    public function setQueSel($queSel): void { $this->queSel = (string) $queSel; }
    public function setQId_activ($Qid_activ): void { $this->Qid_activ = $Qid_activ; }
}
