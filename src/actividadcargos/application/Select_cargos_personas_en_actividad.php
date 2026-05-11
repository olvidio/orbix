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
use frontend\shared\web\BotonesCurso;
use frontend\shared\web\Lista;

/**
 * Widget del dossier `1302` (codigo `cargos_personas_en_actividad`): relacion
 * de actividades en las que una persona tiene un cargo. Instanciado por
 * {@see \src\dossiers\application\DossierTipoFileSuffixResolver::resolveSelectClassFqcn()}.
 *
 * Sucesor de `apps/actividadcargos/model/Select1302.php` + shim
 * `Select_cargos_personas_en_actividad.php`. El control de curso
 * (actuales/curso/todos) se delega en {@see BotonesCurso}.
 */
class Select_cargos_personas_en_actividad
{
    /** @var array<string, array{perm: mixed, nom: mixed}>|null */
    private ?array $ref_perm = null;

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

    private int $modo_curso = 0;

    /** @var int|string|null */
    private $Qid_sel;

    /** @var int|string|null */
    private $Qscroll_id;

    private BotonesCurso $oBotonesCurso;

    /** @var array<string, array{path: string, query: array<string, mixed>}> */
    private array $aLinks_dl = [];

    /** @var array<string, array{path: string, query: array<string, mixed>}> */
    private array $aLinks_otros = [];

    /** @return array<int, array{txt: string, click: string}> */
    private function getBotones(): array
    {
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
            ['name' => _("actividad"), 'width' => 300],
            _("¿Puede ser agd?"),
            _("observaciones"),
        ];
    }

    private function loadValores(): void
    {
        if (!empty($this->a_valores)) {
            return;
        }

        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $this->oBotonesCurso = new BotonesCurso($this->modo_curso);
        $aWhere = $this->oBotonesCurso->getWhere();
        $aOperator = $this->oBotonesCurso->getOperator();

        $oPersona = Persona::findPersonaEnGlobal($this->id_pau);
        if (!is_object($oPersona)) {
            $this->msg_err = "<br>No encuentro a ninguna persona con id_nom: {$this->id_pau} en  " . __FILE__ . ": line " . __LINE__;
            exit($this->msg_err);
        }

        $id_tabla = $oPersona->getId_tabla();
        $oPermDossier = new PermDossier();
        $this->ref_perm = $oPermDossier->perm_activ_pers($id_tabla);

        $this->txt_eliminar = _("¿Está seguro que desea quitar este cargo a esta persona?");
        if (($_SESSION['oPerm']->have_perm_oficina('des')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd'))) {
            $this->txt_eliminar .= "\\n" . _("esto también borrará a esta persona de la lista de asistentes");
            $elim_asis_default = 2;
        } else {
            $elim_asis_default = 1;
        }

        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);

        $cCargosEnActividad = $ActividadCargoRepository->getActividadCargosDeAsistente(
            ['id_nom' => $this->id_pau],
            $aWhere,
            $aOperator
        );

        $this->a_valores = SelectCargosPersonasEnActividadTableData::buildValorRows(
            $mi_sfsv,
            $elim_asis_default,
            $cCargosEnActividad,
            $CargoRepository,
            $ActividadAllRepository,
            $this->ref_perm ?? [],
            $this->Qid_sel,
            $this->Qscroll_id,
        );
    }

    public function getHtml(): string
    {
        $this->loadValores();

        $oTabla = new Lista();
        $oTabla->setId_tabla('select_cargos_personas_en_actividad');
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->a_valores);

        $this->setLinksInsert();

        $web = rtrim(ConfigGlobal::getWeb(), '/');
        $url_cargo_eliminar = $web . '/src/actividadcargos/cargo_eliminar';

        $hash_select_config = [
            'campos_form' => 'modo_curso',
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
            'oBotonesCurso' => $this->oBotonesCurso,
            'hash_select_html' => FormCargosDeActividadHashCompose::selectListaHiddenHtml($hash_select_config),
            'aLinks_dl' => DossierTipoFormLinkSpecsSigning::signLinkMap($this->aLinks_dl),
            'aLinks_otros' => DossierTipoFormLinkSpecsSigning::signLinkMap($this->aLinks_otros),
            'txt_eliminar' => $this->txt_eliminar,
            'bloque' => $this->bloque,
            'url_form' => DossierTipoPublicUrls::relativeFormController((int) $this->id_dossier),
            'url_cargo_eliminar' => $url_cargo_eliminar,
        ];

        return (new ViewNewPhtml('frontend\\actividadcargos\\controller'))
            ->renderizar('select_cargos_personas_en_actividad.phtml', $a_campos, false);
    }

    private function setLinksInsert(): void
    {
        $this->aLinks_dl = [];
        $this->aLinks_otros = [];
        if (empty($this->ref_perm)) {
            return;
        }
        $mi_dele = ConfigGlobal::mi_delef();

        foreach ($this->ref_perm as $clave => $val) {
            if (empty($val['perm'])) {
                continue;
            }
            $aQuery = [
                'mod' => 'nuevo',
                'que_dl' => $mi_dele,
                'pau' => $this->pau,
                'id_tipo' => $clave,
                'obj_pau' => $this->obj_pau,
                'id_dossier' => $this->id_dossier,
                'id_pau' => $this->id_pau,
            ];
            array_walk($aQuery, 'src\\shared\\domain\\helpers\\poner_empty_on_null');
            $this->aLinks_dl[$val['nom']] = DossierTipoPublicUrls::formControllerLinkSpec((int) $this->id_dossier, $aQuery);
        }
        foreach ($this->ref_perm as $clave => $val) {
            if (empty($val['perm'])) {
                continue;
            }
            $aQuery = [
                'mod' => 'nuevo',
                'pau' => $this->pau,
                'id_tipo' => $clave,
                'obj_pau' => $this->obj_pau,
                'id_dossier' => $this->id_dossier,
                'id_pau' => $this->id_pau,
            ];
            array_walk($aQuery, 'src\\shared\\domain\\helpers\\poner_empty_on_null');
            $this->aLinks_otros[$val['nom']] = DossierTipoPublicUrls::formControllerLinkSpec((int) $this->id_dossier, $aQuery);
        }
    }

    public function getId_dossier() { return $this->id_dossier; }
    public function getPau(): string { return $this->pau; }
    public function getObj_pau(): string { return $this->obj_pau; }
    public function getId_pau(): int { return $this->id_pau; }
    public function getPermiso(): int { return $this->permiso; }
    public function getModo_curso(): int { return $this->modo_curso; }

    public function setId_dossier($id_dossier): void { $this->id_dossier = $id_dossier; }
    public function setPau($pau): void { $this->pau = (string)$pau; }
    public function setObj_pau($obj_pau): void { $this->obj_pau = (string)$obj_pau; }
    public function setId_pau($id_pau): void { $this->id_pau = (int)$id_pau; }
    public function setPermiso($permiso): void { $this->permiso = (int)$permiso; }
    public function setModo_curso($modo_curso): void { $this->modo_curso = (int)$modo_curso; }
    public function setQid_sel($Qid_sel): void { $this->Qid_sel = $Qid_sel; }
    public function setQscroll_id($Qscroll_id): void { $this->Qscroll_id = $Qscroll_id; }
    public function setBloque($bloque): void { $this->bloque = (string)$bloque; }
    public function setQueSel($queSel): void { $this->queSel = (string)$queSel; }
}
