<?php

namespace src\actividadcargos\application;

use core\ConfigGlobal;
use dossiers\model\PermDossier;
use frontend\shared\model\ViewNewPhtml;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\dossiers\application\DossierTipoPublicUrls;
use src\personas\domain\entity\Persona;
use web\Hash;
use web\Lista;
use web\TiposActividades;
use function core\is_true;

/**
 * Widget del dossier `3102` (codigo `cargos_de_actividad`): relacion de personas
 * con cargo en una actividad. Instanciado dinamicamente por
 * {@see \src\dossiers\application\DossierTipoFileSuffixResolver::resolveSelectClassFqcn()}
 * desde `apps/dossiers/controller/dossiers_ver.php` (el resolver busca
 * `src/<app>/application/Select_<codigo>.php` tras haber comprobado `apps/`).
 *
 * Sucesor de `apps/actividadcargos/model/Select3102.php` y de su shim
 * `Select_cargos_de_actividad.php`. Los casos `des`/`vcsd` activan el flag
 * `elim_asis` para que al eliminar el cargo tambien se borre la asistencia.
 */
class Select_cargos_de_actividad
{
    /** @var array<string, array{perm: mixed, obj: mixed, nom: mixed}>|null */
    private ?array $a_ref_perm = null;

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

    /** @var int|string|null */
    private $Qid_sel;

    /** @var int|string|null */
    private $Qscroll_id;

    /** @var array<string, string> */
    private array $aLinks_dl = [];

    /** @return array<int, array{txt: string, click: string}> */
    private function getBotones(): array
    {
        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            return [];
        }
        return [
            ['txt' => _("modificar cargo"), 'click' => 'fnjs_mod_cargo(this.form)'],
            ['txt' => _("quitar cargo"),   'click' => 'fnjs_borrar_cargo(this.form)'],
        ];
    }

    /** @return array<int, string|array{name: string, width: int}> */
    private function getCabeceras(): array
    {
        return [
            _("cargo"),
            ['name' => _("nombre y apellidos"), 'width' => 300],
            _("¿Puede ser agd?"),
            _("observaciones"),
        ];
    }

    private function loadValores(): void
    {
        if (!empty($this->a_valores)) {
            return;
        }

        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);

        $oActividad = $ActividadAllRepository->findById($this->id_pau);
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $oPermDossier = new PermDossier();
        $this->a_ref_perm = $oPermDossier->perm_pers_activ($id_tipo_activ);

        $this->txt_eliminar = _("¿Está seguro que desea quitar este cargo a esta persona?");
        $elim_asis_default = 1;
        if (($_SESSION['oPerm']->have_perm_oficina('des')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd'))) {
            $oTipoActiv = new TiposActividades($id_tipo_activ);
            $sasistentes = $oTipoActiv->getAsistentesText();
            if ($sasistentes === 's' || $sasistentes === 'sg') {
                $this->txt_eliminar .= "\\n" . _("esto también borrará a esta persona de la lista de asistentes");
                $elim_asis_default = 2;
            }
        }

        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $cCargosEnActividad = $ActividadCargoRepository->getActividadCargos(['id_activ' => $this->id_pau]);

        $c = 0;
        $a_valores = [];
        foreach ($cCargosEnActividad as $oActividadCargo) {
            $c++;
            $id_schema = $oActividadCargo->getId_schema();
            $id_item = $oActividadCargo->getId_item();
            $id_nom = $oActividadCargo->getId_nom();
            $id_cargo = $oActividadCargo->getId_cargo();
            $oCargo = $CargoRepository->findById($id_cargo);
            $tipo_cargo = '';
            $cargo = '';
            if ($oCargo !== null) {
                $tipo_cargo = $oCargo->getTipoCargoVo()?->value();
                $cargo = $oCargo->getCargoVo()->value();
            }
            if ($tipo_cargo === 'sacd' && $mi_sfsv == 2) {
                continue;
            }

            $oPersona = Persona::findPersonaEnGlobal($id_nom);
            if ($oPersona === null) {
                $this->msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
                continue;
            }

            $nom = $oPersona->getPrefApellidosNombre();
            $ctr_dl = $oPersona->getCentro_o_dl();
            $chk_puede_agd = is_true($oActividadCargo->isPuede_agd()) ? 'si' : 'no';
            $observ = $oActividadCargo->getObserv();

            $permiso = 1;
            if ($id_tabla = $oPersona->getId_tabla()) {
                $a_act = $this->a_ref_perm[$id_tabla] ?? null;
                $permiso = (!empty($a_act) && !empty($a_act['perm'])) ? 3 : 1;
            } else {
                $permiso = 3;
            }

            if ($permiso === 3) {
                // Formato compatible con Select3101 (asistentes): id_nom#id_item#elim_asis#id_schema
                $a_valores[$c]['sel'] = "$id_nom#$id_item#$elim_asis_default#$id_schema";
            } else {
                $a_valores[$c]['sel'] = '';
            }
            $a_valores[$c][1] = $cargo;
            $a_valores[$c][2] = "$nom  ($ctr_dl)";
            $a_valores[$c][3] = $chk_puede_agd;
            $a_valores[$c][4] = $observ;
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
        if ($this->msg_err !== '') {
            echo $this->msg_err;
        }
    }

    public function getHtml(): void
    {
        $this->loadValores();

        $oHashSelect = new Hash();
        $oHashSelect->setCamposNo('sel!mod!scroll_id!refresh');
        $oHashSelect->setArraycamposHidden([
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'obj_pau' => $this->obj_pau,
            'queSel' => $this->queSel,
            'id_dossier' => $this->id_dossier,
            'permiso' => 3,
        ]);

        $oTabla = new Lista();
        $oTabla->setId_tabla('select_cargos_de_actividad');
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->a_valores);

        $this->setLinksInsert();

        $web = rtrim(ConfigGlobal::getWeb(), '/');
        $url_cargo_eliminar = $web . '/src/actividadcargos/cargo_eliminar';

        $a_campos = [
            'oTabla' => $oTabla,
            'oHashSelect' => $oHashSelect,
            'aLinks_dl' => $this->aLinks_dl,
            'txt_eliminar' => $this->txt_eliminar,
            'bloque' => $this->bloque,
            'url_form' => DossierTipoPublicUrls::relativeFormController((int) $this->id_dossier),
            'url_cargo_eliminar' => $url_cargo_eliminar,
        ];

        (new ViewNewPhtml('frontend\\actividadcargos\\controller'))
            ->renderizar('select_cargos_de_actividad.phtml', $a_campos);
    }

    private function setLinksInsert(): void
    {
        $this->aLinks_dl = [];
        if (empty($this->a_ref_perm) || ConfigGlobal::mi_ambito() === 'rstgr') {
            return;
        }
        foreach ($this->a_ref_perm as $val) {
            if (empty($val['perm'])) {
                continue;
            }
            $aQuery = [
                'mod' => 'nuevo',
                'pau' => $this->pau,
                'obj_pau' => $val['obj'],
                'id_dossier' => $this->id_dossier,
                'id_pau' => $this->id_pau,
            ];
            array_walk($aQuery, 'core\\poner_empty_on_null');
            $pagina = DossierTipoPublicUrls::hashedFormControllerQuery((int) $this->id_dossier, $aQuery);
            $nom = sprintf(_("añadir %s"), $val['nom']);
            $this->aLinks_dl[$nom] = $pagina;
        }
    }

    public function getId_dossier() { return $this->id_dossier; }
    public function getPau(): string { return $this->pau; }
    public function getObj_pau(): string { return $this->obj_pau; }
    public function getId_pau(): int { return $this->id_pau; }
    public function getPermiso(): int { return $this->permiso; }

    public function setId_dossier($id_dossier): void { $this->id_dossier = $id_dossier; }
    public function setPau($pau): void { $this->pau = (string)$pau; }
    public function setObj_pau($obj_pau): void { $this->obj_pau = (string)$obj_pau; }
    public function setId_pau($id_pau): void { $this->id_pau = (int)$id_pau; }
    public function setPermiso($permiso): void { $this->permiso = (int)$permiso; }
    public function setQid_sel($Qid_sel): void { $this->Qid_sel = $Qid_sel; }
    public function setQscroll_id($Qscroll_id): void { $this->Qscroll_id = $Qscroll_id; }
    public function setBloque($bloque): void { $this->bloque = (string)$bloque; }
    public function setQueSel($queSel): void { $this->queSel = (string)$queSel; }
}
