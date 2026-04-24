<?php

namespace src\actividadestudios\application;

use src\shared\config\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\dossiers\application\DossierTipoPublicUrls;
use src\personas\domain\entity\Persona;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use web\Hash;
use web\Lista;

/**
 * Widget del dossier `3005` (codigo `asignaturas_de_una_actividad`):
 * asignaturas impartidas en una actividad de estudios, con profesor, tipo,
 * estado de aviso y fechas.
 *
 * Sucesor de `apps/actividadestudios/model/Select3005.php`. Instanciado
 * dinamicamente por
 * {@see \src\dossiers\application\DossierTipoFileSuffixResolver::resolveSelectClassFqcn()}.
 */
class Select_asignaturas_de_una_actividad
{
    private string $msg_err = '';
    private array $a_valores = [];
    private string $txt_eliminar = '';
    private string $txt_no_permiso = '';
    private string $bloque = '';

    private string $queSel = '';
    private int $id_dossier = 3005;
    private string $pau = '';
    private string $obj_pau = '';
    private int $id_pau = 0;
    private int $permiso = 1;
    private string $dl_org = '';

    private $Qid_sel;
    private $Qscroll_id;
    private string $LinkInsert = '';

    public function getBotones(): array
    {
        $a = [];
        if ($this->permiso === 3) {
            $a[] = ['txt' => _("modificar"), 'click' => 'fnjs_modificar(this.form)'];
            $a[] = ['txt' => _("quitar asignatura"), 'click' => 'fnjs_borrar_asignatura(this.form)'];
        }
        $a[] = ['txt' => _("actas"), 'click' => 'fnjs_actas(this.form)'];
        return $a;
    }

    public function getCabeceras(): array
    {
        return [
            _("asignatura"),
            _("créditos"),
            _("tipo"),
            _("profesor"),
            _("prof. avisado"),
            _("inicio"),
            _("fin"),
        ];
    }

    public function getValores(): array
    {
        if (empty($this->a_valores)) {
            $this->getTabla();
        }
        return $this->a_valores;
    }

    private function getTabla(): void
    {
        $this->txt_eliminar = _("¿Está seguro que desea quitar esta asignatura?");
        $this->txt_no_permiso = _("No puede modificar una asignatura que depende de otra dl");

        $ActividadAsignaturaRepository = $GLOBALS['container']->get(ActividadAsignaturaRepositoryInterface::class);
        $cActivAsignaturas = $ActividadAsignaturaRepository->getActividadAsignaturas([
            'id_activ' => $this->id_pau, '_ordre' => 'id_asignatura',
        ]);

        $mi_dele = ConfigGlobal::mi_delef();
        $DbSchemaRepository = $GLOBALS['container']->get(DbSchemaRepositoryInterface::class);
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $c = 0;
        $a_valores = [];
        foreach ($cActivAsignaturas as $oActividadAsignatura) {
            $c++;
            $id_activ = $oActividadAsignatura->getId_activ();
            $id_asignatura = $oActividadAsignatura->getId_asignatura();
            $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
            if ($oAsignatura === null) {
                throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
            }
            $nombre_corto = $oAsignatura->getNombre_corto();
            $creditos = $oAsignatura->getCreditos();
            $id_schema = $oActividadAsignatura->getId_schema();
            $cDbSchemas = $DbSchemaRepository->getDbSchemas(['id' => $id_schema]);
            $a_reg = explode('-', $cDbSchemas[0]->getSchema());
            $dl_matricula = substr($a_reg[1], 0, -1);
            if ($dl_matricula !== $this->dl_org) {
                $nombre_corto = "($dl_matricula) $nombre_corto";
            }
            $permiso = ($dl_matricula !== $mi_dele) ? 'false' : 'true';

            $id_profesor = $oActividadAsignatura->getId_profesor();
            if (!empty($id_profesor)) {
                $oPersona = Persona::findPersonaEnGlobal($id_profesor);
                if ($oPersona === null) {
                    $this->msg_err .= "<br>No encuentro a nadie con id_nom: $id_profesor (profesor) en  " . __FILE__ . ": line " . __LINE__;
                    $nom = '';
                } else {
                    $nom = $oPersona->getPrefApellidosNombre();
                }
            } else {
                $nom = '';
            }
            $aviso = match ($oActividadAsignatura->getAvis_profesor()) {
                'a' => _("avisado"),
                'c' => _("confirmado"),
                default => '',
            };
            $tipo = $oActividadAsignatura->getTipo();
            $f_ini = $oActividadAsignatura->getF_ini()?->getFromLocal();
            $f_fin = $oActividadAsignatura->getF_fin()?->getFromLocal();

            $a_valores[$c]['sel'] = "$id_activ#$id_asignatura#$permiso";
            $a_valores[$c][1] = $nombre_corto;
            $a_valores[$c][2] = $creditos;
            $a_valores[$c][3] = $tipo;
            $a_valores[$c][4] = $nom;
            $a_valores[$c][5] = $aviso;
            $a_valores[$c][6] = $f_ini;
            $a_valores[$c][7] = $f_fin;
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

    public function getHtml(): void
    {
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $oActividad = $ActividadAllRepository->findById($this->id_pau);
        $this->dl_org = $oActividad->getDl_org();
        $this->permiso = 3;

        $oHashSelect = new Hash();
        $oHashSelect->setCamposForm('');
        $oHashSelect->setCamposNo('sel!mod!scroll_id!refresh');
        $oHashSelect->setArraycamposHidden([
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'obj_pau' => $this->obj_pau,
            'queSel' => $this->queSel,
            'id_dossier' => $this->id_dossier,
            'permiso' => $this->permiso,
        ]);

        $oTabla = new Lista();
        $oTabla->setId_tabla('select3005');
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->getValores());

        $this->setLinksInsert();

        $web = rtrim(ConfigGlobal::getWeb(), '/');
        $a_campos = [
            'oTabla' => $oTabla,
            'oHashSelect' => $oHashSelect,
            'link_insert' => $this->LinkInsert,
            'txt_eliminar' => $this->txt_eliminar,
            'txt_no_permiso' => $this->txt_no_permiso,
            'bloque' => $this->bloque,
            'url_form' => $web . '/' . DossierTipoPublicUrls::relativeFormController($this->id_dossier),
            'url_actividad_asignatura_eliminar' => $web . '/src/actividadestudios/actividad_asignatura_eliminar',
        ];

        (new ViewNewPhtml('frontend\\actividadestudios\\controller'))
            ->renderizar('select_asignaturas_de_una_actividad.phtml', $a_campos);
    }

    public function setLinksInsert(): void
    {
        $this->LinkInsert = '';
        if ($this->permiso === 3) {
            $a_dataUrl = [
                'pau' => $this->pau,
                'id_pau' => $this->id_pau,
            ];
            array_walk($a_dataUrl, 'core\\poner_empty_on_null');
            $this->LinkInsert = DossierTipoPublicUrls::hashedFormControllerQuery($this->id_dossier, $a_dataUrl);
        }
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
