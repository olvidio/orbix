<?php

namespace actividadcargos\model;

use actividades\model\entity as actividades;
use actividadcargos\model\entity as actividadcargos;
use dossiers\model as dossiers;
use personas\model\entity as personas;
use core;
use web;
use web\Hash;
use core\ConfigGlobal;

/**
 * Gestiona el dossier 3102: Cargos de una actividad
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
class Select3102
{
    // --------- Variables internas de la clase.
    /**
     * array con los permisos (si o no) para añadir las personas (agd, n...)
     * según el tipo de actividad de que se trate y quién seamos nosotros.
     * @var array $a_ref_perm
     */
    private $a_ref_perm;
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
    /* @var $queSel integer */
    private $queSel;
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
     * @var integer permiso
     */
    private $permiso;

    // ------ Variables para mantener la selección de la grid al volver atras
    private $Qid_sel;
    private $Qscroll_id;


    private function getBotones()
    {
        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $a_botones = [];
        } else {
            $a_botones = array(
                array('txt' => _("modificar cargo"), 'click' => "fnjs_mod_cargo(this.form)"),
                array('txt' => _("quitar cargo"), 'click' => "fnjs_borrar_cargo(this.form)")
            );
        }
        return $a_botones;
    }

    private function getCabeceras()
    {
        $a_cabeceras = array(_("cargo"),
            array('name' => _("nombre y apellidos"), 'width' => 300),
            _("¿puede ser agd?"),
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
        $oCargosEnActividad = new actividadcargos\GestorActividadCargo();

        // Permisos según el tipo de actividad
        $oActividad = new actividades\Actividad($this->id_pau);
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $oPermDossier = new dossiers\PermDossier();
        $a_ref_perm = $oPermDossier->perm_pers_activ($id_tipo_activ);
        $this->a_ref_perm = $a_ref_perm;

        $c = 0;
        $a_valores = array();
        $cCargosEnActividad = $oCargosEnActividad->getActividadCargos(array('id_activ' => $this->id_pau));
        $mi_sfsv = core\ConfigGlobal::mi_sfsv();
        foreach ($cCargosEnActividad as $oActividadCargo) {
            $c++;
            $id_schema = $oActividadCargo->getId_schema();
            $id_item = $oActividadCargo->getId_item();
            $id_nom = $oActividadCargo->getId_nom();
            $id_cargo = $oActividadCargo->getId_cargo();
            $oCargo = new actividadcargos\Cargo(array('id_cargo' => $id_cargo));
            $tipo_cargo = $oCargo->getTipo_cargo();
            // para los sacd en sf
            if ($tipo_cargo == 'sacd' && $mi_sfsv == 2) {
                continue;
            }
            $oPersona = personas\Persona::NewPersona($id_nom);
            if (!is_object($oPersona)) {
                $this->msg_err .= "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
                continue;
            }
            $oCargo = new actividadcargos\Cargo($id_cargo);

            $nom = $oPersona->getPrefApellidosNombre();

            $cargo = $oCargo->getCargo();
            $puede_agd = $oActividadCargo->getPuede_agd();
            $observ = $oActividadCargo->getObserv();
            $ctr_dl = $oPersona->getCentro_o_dl();
            // permisos (añado caso de cargo sin nombre = todos permiso)
            if ($id_tabla = $oPersona->getId_tabla()) {
                $a_act = $a_ref_perm[$id_tabla];
                if ($a_act["perm"]) {
                    $permiso = 3;
                } else {
                    $permiso = 1;
                }
            } else {
                $permiso = 3;
            }
            $puede_agd == 't' ? $chk_puede_agd = "si" : $chk_puede_agd = "no";

            // Para los de des, elimino el cargo y la asistencia. Para el resto, sólo el cargo (no la asistencia).
            $this->txt_eliminar = _("¿Está seguro que desea quitar este cargo a esta persona?");
            if (($_SESSION['oPerm']->have_perm_oficina('des')) || ($_SESSION['oPerm']->have_perm_oficina('vcsd'))) {
                $this->txt_eliminar .= "\\n";
                $this->txt_eliminar .= _("esto también borrará a esta persona de la lista de asistentes");
                $eliminar = 2;
            } else {
                $eliminar = 1;
            }

            if ($permiso == 3) {
                $a_valores[$c]['sel'] = "$id_item#$eliminar#$id_schema";
            } else {
                $a_valores[$c]['sel'] = "";
            }

            $a_valores[$c][1] = $cargo;
            $a_valores[$c][2] = "$nom  ($ctr_dl)";
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
        if (!empty($this->msg_err)) {
            echo $this->msg_err;
        }
    }

    public function getHtml()
    {
        $oHashSelect = new Hash();
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
        $oTabla = new web\Lista();
        $oTabla->setId_tabla('select3102');
        $oTabla->setCabeceras($this->getCabeceras());
        $oTabla->setBotones($this->getBotones());
        $oTabla->setDatos($this->getValores());

        // para que genere las variables $aLink
        $this->setLinksInsert();

        $a_campos = ['oTabla' => $oTabla,
            'oHashSelect' => $oHashSelect,
            'aLinks_dl' => $this->aLinks_dl,
            'txt_eliminar' => $this->txt_eliminar,
            'bloque' => $this->bloque,
        ];

        $oView = new core\View(__NAMESPACE__);
        $oView->renderizar('select3102.phtml', $a_campos);
    }

    private function setLinksInsert()
    {
        $this->aLinks_dl = array();
        $a_ref_perm = $this->a_ref_perm;
        if (empty($a_ref_perm) || ConfigGlobal::mi_ambito() === 'rstgr') { // si es nulo, no tengo permisos de ningún tipo
            return '';
        }
        reset($a_ref_perm);
        foreach ($a_ref_perm as $clave => $val) {
            $perm = $val["perm"];
            $obj_pau = $val["obj"];
            $nom = $val["nom"];
            if (!empty($perm)) {
                $aQuery = array('mod' => 'nuevo',
                    'pau' => $this->pau,
                    'obj_pau' => $obj_pau,
                    'id_dossier' => $this->id_dossier, //Para que al volver a la pagina 'dossiers_ver' sepa cual mostrar.
                    'id_pau' => $this->id_pau);
                // el hppt_build_query no pasa los valores null
                if (is_array($aQuery)) {
                    array_walk($aQuery, 'core\poner_empty_on_null');
                }
                $pagina = web\Hash::link('apps/actividadcargos/controller/form_3102.php?' . http_build_query($aQuery));
                $nom2 = sprintf(_("añadir %s"), $nom);
                $this->aLinks_dl[$nom2] = $pagina;
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
