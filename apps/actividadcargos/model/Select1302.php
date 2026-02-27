<?php

namespace actividadcargos\model;

use core\ConfigGlobal;
use core\ViewPhtml;
use dossiers\model\PermDossier;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\personas\domain\entity\Persona;
use web\BotonesCurso;
use web\Hash;
use web\Lista;
use function core\is_true;

/**
 * Gestiona el dossier 1302: Cargos de una persona en actividades
 *
 * En el caso de ser "des" o "vcsd" al quitar cargo, también elimino la asistencia.
 * abajo se añaden los botones para añadir una nueva persona-cargo.
 *
 * @package    orbix
 * @subpackage    actividadcargos
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 */
class Select1302
{
    // --------- Variables internas de la clase.
    /**
     * array con los permisos (si o no) para asignar las actividades (según el tipo: nº)
     * según el tipo de persona de que se trate y quién seamos nosotros.
     * @var array $ref_perm
     */
    private $ref_perm;
    /* @var $mwg_err string */
    private $msg_err;
    /* @var $a_valores array */
    private $a_valores;
    /**
     * Para pasar a la vista, aparece como alerta antes de ejecutarse
     * @var string $txt_eliminar
     */
    private $txt_eliminar;
    /* @var $bloque string  necesario para el script */
    private $bloque;

    // ---------- Variables requeridas
    private string $queSel;
    /* @var $id_dossier integer */
    private $id_dossier;
    /* @var $pau string */
    private $pau;
    /* @var $obj_pau string */
    private $obj_pau;
    /* @var $id_pau integer */
    private $id_pau;
    /**
     * 3: para todo, 2, 1:solo lectura
     * @var integer $permiso
     */
    private $permiso;
    /**
     * Cambia la selección de actividades según actuales(1), curso(2), todos los cursos(3)
     * @var integer $modo_curso
     */
    private $modo_curso;

    // ------ Variables para mantener la selección de la grid al volver atras
    private $Qid_sel;
    private $Qscroll_id;
    private BotonesCurso $oBotonesCurso;
    /**
     * @var array|mixed
     */
    private mixed $aLinks_dl;
    /**
     * @var array|mixed
     */
    private mixed $aLinks_otros;

    private function getBotones()
    {
        $a_botones = array(
            array('txt' => _("modificar cargo"), 'click' => "fnjs_mod_cargo(this.form)"),
            array('txt' => _("quitar cargo"), 'click' => "fnjs_borrar_cargo(this.form)")
        );
        return $a_botones;
    }

    private function getCabeceras()
    {
        $a_cabeceras = array(_("cargo"),
            array('name' => _("actividad"), 'width' => 300),
            _("¿Puede ser agd?"),
            _("observaciones")
        );
        return $a_cabeceras;
    }

    private function getValores()
    {
        if (empty($this->a_valores)) {
            $this->getTabla();
        }
        return $this->a_valores;
    }

    private function getTabla()
    {
        $mi_sfsv = ConfigGlobal::mi_sfsv();

        $this->oBotonesCurso = new BotonesCurso($this->modo_curso);
        $aWhere = $this->oBotonesCurso->getWhere();
        $aOperator = $this->oBotonesCurso->getOperator();

        $oPersona = Persona::findPersonaEnGlobal($this->id_pau);
        if (!is_object($oPersona)) {
            $this->msg_err = "<br>No encuentro a ninguna persona con id_nom: $this->id_pau en  " . __FILE__ . ": line " . __LINE__;
            exit ($this->msg_err);
        }

        // permisos Según el tipo de persona: n, agd, s
        $id_tabla = $oPersona->getId_tabla();
        $oPermDossier = new PermDossier();
        $ref_perm = $oPermDossier->perm_activ_pers($id_tabla);
        $this->ref_perm = $ref_perm;

        // Para los de des, elimino el cargo y la asistencia. Para el resto, sólo el cargo (no la asistencia).
        $this->txt_eliminar = _("¿Está seguro que desea quitar este cargo a esta persona?");
        if (($_SESSION['oPerm']->have_perm_oficina('des')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd'))) {
            $this->txt_eliminar .= "\\n";
            $this->txt_eliminar .= _("esto también borrará a esta persona de la lista de asistentes");
            $eliminar = 2;
        } else {
            $eliminar = 1;
        }

        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);

        $c = 0;
        $a_valores = [];
        $cCargosEnActividad = $ActividadCargoRepository->getActividadCargosDeAsistente(array('id_nom' => $this->id_pau), $aWhere, $aOperator);
        $CargoRepository = $GLOBALS['container']->get(CargoRepositoryInterface::class);
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        foreach ($cCargosEnActividad as $oActividadCargo) {
            $c++;
            $id_item = $oActividadCargo->getId_item();
            $id_activ = $oActividadCargo->getId_activ();
            $id_cargo = $oActividadCargo->getId_cargo();
            $oCargo = $CargoRepository->findById($id_cargo);
            $tipo_cargo = $oCargo->getTipoCargoVo()?->value();
            $cargo = $oCargo->getCargoVo()->value();
            // para los sacd en sf
            if ($tipo_cargo === 'sacd' && $mi_sfsv == 2) {
                continue;
            }

            $oActividad = $ActividadAllRepository->findById($id_activ);
            $nom_activ = $oActividad->getNom_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();

            $puede_agd = $oActividadCargo->isPuede_agd();
            $observ = $oActividadCargo->getObserv();

            is_true($puede_agd) ? $chk_puede_agd = "si" : $chk_puede_agd = "no";

            // para modificar.
            $id_tipo = substr($id_tipo_activ, 0, 3); //cojo los 3 primeros dígitos
            $act = !empty($ref_perm[$id_tipo]) ? $ref_perm[$id_tipo] : '';

            if (!empty($act["perm"])) {
                $permiso = 3;
            } else {
                $permiso = 1;
            }


            if ($permiso == 3) {
                $a_valores[$c]['sel'] = "$id_item#$eliminar";
            } else {
                $a_valores[$c]['sel'] = "";
            }

            $a_valores[$c][1] = $cargo;
            $a_valores[$c][2] = "$nom_activ";
            $a_valores[$c][3] = $chk_puede_agd;
            $a_valores[$c][4] = $observ;
        }
        if (!empty($a_valores)) {
            // Estas dos variables vienen de la pagina 'padre' dossiers_ver.php
            // las pongo al final, porque al contar los valores del array se despista.
            if (isset($this->Qid_sel) && !empty($this->Qid_sel)) {
                $a_valores['select'] = $this->Qid_sel;
            }
            if (isset($this->Qscroll_id) && !empty($this->Qscroll_id)) {
                $a_valores['scroll_id'] = $this->Qscroll_id;
            }
        }

        $this->a_valores = $a_valores;
    }

    public function getHtml()
    {
        $oHashSelect = new Hash();


        $oHashSelect->setCamposForm('modo_curso');
        $oHashSelect->setCamposNo('sel!mod!scroll_id!refresh');
        $a_camposHidden = array(
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'obj_pau' => $this->obj_pau,
            'queSel' => $this->queSel,
            'id_dossier' => $this->id_dossier,
            'permiso' => 3,
        );
        $oHashSelect->setArraycamposHidden($a_camposHidden);

        //Hay que ponerlo antes, para que calcule los chk.
        $oTabla = new Lista();
        $oTabla->setId_tabla('select1302');
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->getValores());

        // para que genere las variables $aLink
        $this->setLinksInsert();

        $a_campos = ['oTabla' => $oTabla,
            'oBotonesCurso' => $this->oBotonesCurso,
            'oHashSelect' => $oHashSelect,
            'aLinks_dl' => $this->aLinks_dl,
            'aLinks_otros' => $this->aLinks_otros,
            'txt_eliminar' => $this->txt_eliminar,
            'bloque' => $this->bloque,
        ];

        $oView = new ViewPhtml(__NAMESPACE__);
        $oView->renderizar('select1302.phtml', $a_campos);
    }

    private function setLinksInsert()
    {
        $this->aLinks_dl = [];
        $this->aLinks_otros = [];
        $ref_perm = $this->ref_perm;
        if (empty($ref_perm)) { // si es nulo, no tengo permisos de ningún tipo
            return '';
        }
        $mi_dele = ConfigGlobal::mi_delef();
        reset($ref_perm);
        foreach ($ref_perm as $clave => $val) {
            $permis = $val["perm"];
            $nom = $val["nom"];
            if (!empty($permis)) {
                $aQuery = array('mod' => 'nuevo',
                    'que_dl' => $mi_dele,
                    'pau' => $this->pau,
                    'id_tipo' => $clave,
                    'obj_pau' => $this->obj_pau,
                    'id_dossier' => $this->id_dossier, //Para que al volver a la pagina 'dossiers_ver' sepa cual mostrar.
                    'id_pau' => $this->id_pau);
                // el hppt_build_query no pasa los valores null
                if (is_array($aQuery)) {
                    array_walk($aQuery, 'core\poner_empty_on_null');
                }
                $pagina = Hash::link('apps/actividadcargos/controller/form_1302.php?' . http_build_query($aQuery));
                $this->aLinks_dl[$nom] = $pagina;
            }
        }
        reset($ref_perm);
        foreach ($ref_perm as $clave => $val) {
            $permis = $val["perm"];
            $nom = $val["nom"];
            if (!empty($permis)) {
                $aQuery = array('mod' => 'nuevo',
                    'pau' => $this->pau,
                    'id_tipo' => $clave,
                    'obj_pau' => $this->obj_pau,
                    'id_dossier' => $this->id_dossier, //Para que al volver a la pagina 'dossiers_ver' sepa cual mostrar.
                    'id_pau' => $this->id_pau);
                // el hppt_build_query no pasa los valores null
                if (is_array($aQuery)) {
                    array_walk($aQuery, 'core\poner_empty_on_null');
                }
                $pagina = Hash::link('apps/actividadcargos/controller/form_1302.php?' . http_build_query($aQuery));
                $this->aLinks_otros[$nom] = $pagina;
            }
        }
    }

    public function getId_dossier()
    {
        return $this->id_dossier;
    }

    public function getPau()
    {
        return $this->pau;
    }

    public function getObj_pau()
    {
        return $this->obj_pau;
    }

    public function getId_pau()
    {
        return $this->id_pau;
    }

    public function getPermiso()
    {
        return $this->permiso;
    }

    public function getModo_curso()
    {
        return $this->modo_curso;
    }

    public function setId_dossier($Qid_dossier)
    {
        $this->id_dossier = $Qid_dossier;
    }

    public function setPau($Qpau)
    {
        $this->pau = $Qpau;
    }

    public function setObj_pau($Qobj_pau)
    {
        $this->obj_pau = $Qobj_pau;
    }

    public function setId_pau($Qid_pau)
    {
        $this->id_pau = $Qid_pau;
    }

    public function setPermiso($Qpermiso)
    {
        $this->permiso = $Qpermiso;
    }

    public function setModo_curso($modo_curso)
    {
        $this->modo_curso = $modo_curso;
    }

    public function setQid_sel($Qid_sel)
    {
        $this->Qid_sel = $Qid_sel;
    }

    public function setQscroll_id($Qscroll_id)
    {
        $this->Qscroll_id = $Qscroll_id;
    }

    public function setBloque($bloque)
    {
        $this->bloque = $bloque;
    }

    public function setQueSel($queSel)
    {
        $this->queSel = $queSel;
    }


}
