<?php

namespace src\actividadcargos\application;

use src\shared\config\ConfigGlobal;
use src\dossiers\application\PermDossier;
use frontend\shared\model\ViewNewPhtml;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use frontend\dossiers\helpers\DossierTipoFormLinkSpecsSigning;
use src\dossiers\application\DossierTipoPublicUrls;
use src\personas\domain\entity\Persona;
use frontend\actividadcargos\helpers\FormCargosDeActividadHashCompose;
use frontend\shared\web\Lista;
use src\actividades\domain\entity\TiposActividades;

/**
 * Widget del dossier `3102` (codigo `cargos_de_actividad`): relacion de personas
 * con cargo en una actividad. Instanciado dinamicamente por
 * {@see \src\dossiers\application\DossierTipoFileSuffixResolver::resolveSelectClassFqcn()}
 * desde `frontend/dossiers/controller/dossiers_ver.php` (el resolver busca
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

    /** @var array<string, array{path: string, query: array<string, mixed>}> */
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
        $dl_org = $oActividad->getDl_org();
        $dl_propia = (ConfigGlobal::mi_delef() === $dl_org);
        $oPermDossier = new PermDossier();
        $this->a_ref_perm = $oPermDossier->perm_pers_activ($id_tipo_activ, $dl_propia);

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

        $result = SelectCargosDeActividadTableData::buildValorRows(
            $elim_asis_default,
            $mi_sfsv,
            $cCargosEnActividad,
            $CargoRepository,
            static fn (?int $id_nom) => Persona::findPersonaEnGlobal($id_nom),
            $this->a_ref_perm ?? [],
            $this->Qid_sel,
            $this->Qscroll_id,
        );
        $this->a_valores = $result['a_valores'];
        $this->msg_err .= $result['msg_err'];
        if ($this->msg_err !== '') {
            echo $this->msg_err;
        }
    }

    public function getHtml(): string
    {
        $this->loadValores();

        $oTabla = new Lista();
        $oTabla->setId_tabla('select_cargos_de_actividad');
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->a_valores);

        $this->setLinksInsert();

        $web = rtrim(ConfigGlobal::getWeb(), '/');
        $url_cargo_eliminar = $web . '/src/actividadcargos/cargo_eliminar';

        $hash_select_config = [
            'campos_no' => 'sel!mod!scroll_id!refresh',
            'campos_hidden' => [
                'pau' => $this->pau,
                'id_pau' => $this->id_pau,
                'obj_pau' => $this->obj_pau,
                'queSel' => $this->queSel,
                'id_dossier' => $this->id_dossier,
                'permiso' => 3,
            ],
        ];

        $a_campos = [
            'oTabla' => $oTabla,
            'hash_select_html' => FormCargosDeActividadHashCompose::selectListaHiddenHtml($hash_select_config),
            'aLinks_dl' => DossierTipoFormLinkSpecsSigning::signLinkMap($this->aLinks_dl),
            'txt_eliminar' => $this->txt_eliminar,
            'bloque' => $this->bloque,
            'url_form' => DossierTipoPublicUrls::relativeFormController((int) $this->id_dossier),
            'url_cargo_eliminar' => $url_cargo_eliminar,
        ];

        return (new ViewNewPhtml('frontend\\actividadcargos\\controller'))
            ->renderizar('select_cargos_de_actividad.phtml', $a_campos, false);
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
            array_walk($aQuery, 'src\\shared\\domain\\helpers\\poner_empty_on_null');
            $nom = sprintf(_("añadir %s"), $val['nom']);
            $this->aLinks_dl[$nom] = DossierTipoPublicUrls::formControllerLinkSpec((int) $this->id_dossier, $aQuery);
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
