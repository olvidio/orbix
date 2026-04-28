<?php

namespace src\actividadestudios\application;

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\dossiers\application\DossierTipoPublicUrls;
use src\personas\domain\entity\Persona;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

/**
 * Widget del dossier `3103` (codigo `matriculas_de_una_actividad`):
 * listado de matriculas de una actividad, agrupadas por asignatura.
 *
 * Sucesor de `apps/actividadestudios/model/Select3103.php`. Instanciado
 * dinamicamente por
 * {@see \src\dossiers\application\DossierTipoFileSuffixResolver::resolveSelectClassFqcn()}.
 */
class Select_matriculas_de_una_actividad
{
    private string $msg_err = '';
    private string $nom_activ = '';
    private array $a_valores = [];
    private array $a_grupos = [];
    private string $txt_eliminar = '';
    private string $bloque = '';

    private string $queSel = '';
    private int $id_dossier = 3103;
    private string $pau = '';
    private string $obj_pau = '';
    private int $id_pau = 0;
    private int $permiso = 1;

    private $Qid_sel;
    private $Qscroll_id;

    public function getBotones(): array
    {
        return [
            ['txt' => _("borrar matrícula"), 'click' => 'fnjs_borrar(this.form)'],
        ];
    }

    public function getCabeceras(): array
    {
        return [
            _("asignatura"),
            _("alumno"),
        ];
    }

    public function getValores(): array
    {
        if (empty($this->a_valores)) {
            $this->getTabla();
        }
        return $this->a_valores;
    }

    public function getGrupos(): array
    {
        if (empty($this->a_grupos)) {
            $this->getTabla();
        }
        return $this->a_grupos;
    }

    private function getTabla(): void
    {
        $mi_dele = ConfigGlobal::mi_delef();
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $oActividad = $ActividadAllRepository->findById($this->id_pau);
        $this->nom_activ = $oActividad->getNom_activ();
        $dl_org = $oActividad->getDl_org();

        if ($mi_dele == $dl_org) {
            $this->permiso = 3;
            $repoAsignaturas = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);
        } else {
            $this->permiso = 1;
            $repoAsignaturas = $GLOBALS['container']->get(ActividadAsignaturaRepositoryInterface::class);
        }
        $cActividadAsignaturas = $repoAsignaturas->getActividadAsignaturas([
            'id_activ' => $this->id_pau,
            '_ordre' => 'id_asignatura',
        ]);

        if (is_array($cActividadAsignaturas) && count($cActividadAsignaturas) === 0) {
            echo _("esta actividad no tiene ninguna asignatura");
            return;
        }

        $a = 0;
        $msg_err = '';
        $aGrupos = [];
        $a_valores = [];
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $MatriculaDlRepository = $GLOBALS['container']->get(MatriculaDlRepositoryInterface::class);
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

            $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
            if ($oAsignatura === null) {
                throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
            }
            $nombre_corto = $oAsignatura->getNombre_corto();
            $aGrupos[$id_asignatura] = $nombre_corto;

            $cMatriculas = $MatriculaDlRepository->getMatriculas([
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

    public function getHtml(): void
    {
        $this->txt_eliminar = _("¿Está seguro que desea quitar esta matrícula?");

        $oHashSelect = new HashFront();
        $oHashSelect->setCamposForm('');
        $oHashSelect->setCamposNo('sel!mod!scroll_id!refresh');
        $oHashSelect->setArraycamposHidden([
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'obj_pau' => $this->obj_pau,
            'queSel' => $this->queSel,
            'id_dossier' => $this->id_dossier,
            'permiso' => $this->permiso,
            'bloque' => $this->bloque,
        ]);

        $oTabla = new Lista();
        $oTabla->setGrupos($this->getGrupos());
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->getValores());

        $web = rtrim(ConfigGlobal::getWeb(), '/');
        $a_campos = [
            'oHashSelect' => $oHashSelect,
            'oTabla' => $oTabla,
            'txt_eliminar' => $this->txt_eliminar,
            'nom_activ' => $this->nom_activ,
            'url_form' => $web . '/' . DossierTipoPublicUrls::relativeFormController(1303),
            'url_matricula_eliminar' => $web . '/src/actividadestudios/matricula_eliminar',
        ];

        (new ViewNewPhtml('frontend\\actividadestudios\\controller'))
            ->renderizar('select_matriculas_de_una_actividad.phtml', $a_campos);
    }

    public function getId_dossier() { return $this->id_dossier; }
    public function getPau(): string { return $this->pau; }
    public function getObj_pau(): string { return $this->obj_pau; }
    public function getId_pau(): int { return $this->id_pau; }
    public function getPermiso(): int { return $this->permiso; }

    public function setId_dossier($id_dossier): void { $this->id_dossier = (int) $id_dossier; }
    public function setPau($pau): void { $this->pau = (string) $pau; }
    public function setObj_pau($obj_pau): void { $this->obj_pau = (string) $obj_pau; }
    public function setId_pau($id_pau): void { $this->id_pau = (int) $id_pau; }
    public function setPermiso($permiso): void { $this->permiso = (int) $permiso; }
    public function setQid_sel($Qid_sel): void { $this->Qid_sel = $Qid_sel; }
    public function setQscroll_id($Qscroll_id): void { $this->Qscroll_id = $Qscroll_id; }
    public function setBloque($bloque): void { $this->bloque = (string) $bloque; }
    public function setQueSel($queSel): void { $this->queSel = (string) $queSel; }
}
