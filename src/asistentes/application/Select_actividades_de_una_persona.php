<?php

namespace src\asistentes\application;

use src\shared\config\ConfigGlobal;
use src\dossiers\application\PermDossier;
use frontend\shared\model\ViewNewPhtml;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\dossiers\application\DossierTipoPublicUrls;
use src\personas\domain\entity\Persona;
use frontend\shared\web\BotonesCurso;
use web\Hash;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;
use src\actividades\domain\entity\TiposActividades;
use function src\shared\domain\helpers\is_true;

/**
 * Widget del dossier `1301` (codigo `actividades_de_una_persona`):
 * actividades a las que asiste una persona.
 *
 * Sucesor de `apps/asistentes/model/Select1301.php`. Instanciado dinamicamente por
 * {@see \src\dossiers\application\DossierTipoFileSuffixResolver::resolveSelectClassFqcn()}.
 */
class Select_actividades_de_una_persona
{
    private const ID_TIPO_DOSSIER = 1301;

    private $ref_perm;
    private $msg_err;
    private $a_valores;
    private $txt_eliminar;
    private $bloque;

    private string $queSel = '';
    private $id_dossier;
    private $pau;
    private $obj_pau;
    private $id_pau;
    private $permiso;
    private $modo_curso;

    private $Qid_sel;
    private $Qscroll_id;
    private BotonesCurso $oBotonesCurso;
    private mixed $aLinks_dl;
    private mixed $aLinks_otros;
    private mixed $status;

    private function getBotones(): array
    {
        return [
            ['txt' => _("modificar asistencia"), 'click' => "fnjs_modificar(this.form)"],
            ['txt' => _("borrar asistencia"), 'click' => "fnjs_borrar(this.form)"],
        ];
    }

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

    private function getValores()
    {
        if (empty($this->a_valores)) {
            $this->getTabla();
        }
        return $this->a_valores;
    }

    private function getTabla(): void
    {
        $mi_sfsv = ConfigGlobal::mi_sfsv();

        $this->oBotonesCurso = new BotonesCurso($this->modo_curso);
        $aWhere = $this->oBotonesCurso->getWhere();
        $aOperator = $this->oBotonesCurso->getOperator();

        $oPersona = Persona::findPersonaEnGlobal($this->id_pau);
        if (!is_object($oPersona)) {
            $this->msg_err = "<br>No encuentro a ninguna persona con id_nom: $this->id_pau en  " . __FILE__ . ": line " . __LINE__;
            exit($this->msg_err);
        }
        $id_tabla = $oPersona->getId_tabla();
        $oPermDossier = new PermDossier();
        $this->ref_perm = $oPermDossier->perm_activ_pers($id_tabla);

        $i = 0;
        $a_valores = [];
        $aWhereNom = ['id_nom' => $this->id_pau];
        $aOperadorNom = [];
        $service = $GLOBALS['container']->get(AsistenteActividadService::class);
        $cActividadesAsistente = $service->getActividadesDeAsistente($aWhereNom, $aOperadorNom, $aWhere, $aOperator, TRUE);
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        foreach ($cActividadesAsistente as $oAsistente) {
            $i++;
            $id_activ = $oAsistente->getId_activ();
            $oActividad = $ActividadAllRepository->findById($id_activ);
            $nom_activ = $oActividad->getNom_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $dl_org = $oActividad->getDl_org();
            $f_ini = $oActividad->getF_ini()?->getFromLocal();
            $f_fin = $oActividad->getF_fin()?->getFromLocal();

            $propio = $oAsistente->isPropio();
            $falta = $oAsistente->isFalta();
            $est_ok = $oAsistente->isEst_ok();
            $observ = $oAsistente->getObserv();

            $oTipoActividad = new TiposActividades($id_tipo_activ);
            $isfsv = $oTipoActividad->getSfsvId();
            if ($mi_sfsv != $isfsv && !($_SESSION['oPerm']->have_perm_oficina('des'))) {
                $ssfsv = $oTipoActividad->getSfsvText();
                $sactividad = $oTipoActividad->getActividadText();
                $nom_activ = "$ssfsv $sactividad";
            }
            $id_tipo = substr($id_tipo_activ, 0, 3);
            $act = !empty($this->ref_perm[$id_tipo]) ? $this->ref_perm[$id_tipo] : '';
            $permiso = !empty($act["perm"]) ? 3 : 1;

            is_true($propio) ? $chk_propio = "si" : $chk_propio = "no";
            is_true($falta) ? $chk_falta = "si" : $chk_falta = "no";
            is_true($est_ok) ? $chk_est_ok = "si" : $chk_est_ok = "no";

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

    public function getHtml()
    {
        $this->txt_eliminar = _("¿Está seguro que desea borrar a esta persona de esta actividad?");
        $oPosicion = new Posicion();
        $stack = $oPosicion->getStack(0);

        $oHashSelect = new Hash();
        $oHashSelect->setCamposForm('modo_curso');
        $oHashSelect->setCamposNo('sel!mod!scroll_id!refresh');
        $oHashSelect->setArraycamposHidden([
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'obj_pau' => $this->obj_pau,
            'queSel' => $this->queSel,
            'id_dossier' => $this->id_dossier,
            'permiso' => 3,
            'stack' => $stack,
        ]);

        $oTabla = new Lista();
        $oTabla->setId_tabla('select_actividades_de_una_persona');
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->getValores());

        $this->setLinksInsert();

        $web = rtrim(ConfigGlobal::getWeb(), '/');
        $url_form = $web . '/' . DossierTipoPublicUrls::relativeFormController(self::ID_TIPO_DOSSIER);
        $url_eliminar = $web . '/src/asistentes/asistente_eliminar';

        $a_campos = [
            'oTabla' => $oTabla,
            'oBotonesCurso' => $this->oBotonesCurso,
            'oHashSelect' => $oHashSelect,
            'aLinks_dl' => $this->aLinks_dl,
            'aLinks_otros' => $this->aLinks_otros,
            'txt_eliminar' => $this->txt_eliminar,
            'bloque' => $this->bloque,
            'url_form' => $url_form,
            'url_eliminar' => $url_eliminar,
        ];

        $oView = new ViewNewPhtml('frontend\\asistentes\\controller');
        $oView->renderizar('select_actividades_de_una_persona.phtml', $a_campos);
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
            $nom = $val["nom"];
            $aQuery = [
                'mod' => 'nuevo',
                'que_dl' => $mi_dele,
                'pau' => $this->pau,
                'id_tipo' => $clave,
                'obj_pau' => $this->obj_pau,
                'id_dossier' => $this->id_dossier,
                'id_pau' => $this->id_pau,
            ];
            $this->aLinks_dl[$nom] = DossierTipoPublicUrls::hashedFormControllerQuery(self::ID_TIPO_DOSSIER, $aQuery);
        }
        reset($ref_perm);
        foreach ($ref_perm as $clave => $val) {
            if (empty($val["perm"])) {
                continue;
            }
            $nom = $val["nom"];
            $aQuery = [
                'mod' => 'nuevo',
                'pau' => $this->pau,
                'id_tipo' => $clave,
                'obj_pau' => $this->obj_pau,
                'id_dossier' => $this->id_dossier,
                'id_pau' => $this->id_pau,
            ];
            $this->aLinks_otros[$nom] = DossierTipoPublicUrls::hashedFormControllerQuery(self::ID_TIPO_DOSSIER, $aQuery);
        }
    }

    public function setModo_curso($modo_curso): void
    {
        if (empty($modo_curso)) {
            $modo_curso = 1;
        }
        $this->modo_curso = $modo_curso;
    }

    public function getId_dossier() { return $this->id_dossier; }
    public function getPau() { return $this->pau; }
    public function getObj_pau() { return $this->obj_pau; }
    public function getId_pau() { return $this->id_pau; }
    public function getPermiso() { return $this->permiso; }
    public function getStatus() { return $this->status; }

    public function setId_dossier($Qid_dossier): void { $this->id_dossier = $Qid_dossier; }
    public function setPau($Qpau): void { $this->pau = $Qpau; }
    public function setObj_pau($Qobj_pau): void { $this->obj_pau = $Qobj_pau; }
    public function setId_pau($Qid_pau): void { $this->id_pau = $Qid_pau; }
    public function setPermiso($Qpermiso): void { $this->permiso = $Qpermiso; }
    public function setStatus($Qstatus): void { $this->status = $Qstatus; }
    public function setQid_sel($Qid_sel): void { $this->Qid_sel = $Qid_sel; }
    public function setQscroll_id($Qscroll_id): void { $this->Qscroll_id = $Qscroll_id; }
    public function setBloque($bloque): void { $this->bloque = $bloque; }
    public function setQueSel($queSel): void { $this->queSel = (string) $queSel; }
}
