<?php

namespace asistentes\model;

use actividades\model\entity as actividades;
use personas\model\entity as personas;
use asistentes\model\entity as asistentes;
use dossiers\model as dossiers;
use core;
use web;

/**
 * Gestiona el dossier 1301: Actividades a las que asiste una persona.
 *
 *
 * @package    orbix
 * @subpackage    asistencias
 * @author    Daniel Serrabou
 * @since        15/5/02.
 * @version 1.0  refactoring: separar vistas
 * @created Mayo 2018
 */
class Select1301
{
    // --------- Variables internas de la clase.
    /**
     * array con los permisos (si o no) para asignar las actividades (según el tipo: nº)
     * según el tipo de persona de que se trate y quién seamos nosotros.
     * @var array $ref_perm
     */
    private $ref_perm;
    /* @var $msg_err string */
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

    private function getBotones()
    {
        $a_botones = array(
            array('txt' => _("modificar asistencia"), 'click' => "fnjs_modificar(this.form)"),
            array('txt' => _("borrar asistencia"), 'click' => "fnjs_borrar(this.form)")
        );
        return $a_botones;
    }

    private function getCabeceras()
    {
        $a_cabeceras = array(array('name' => _("fechas"), 'width' => 150),
            array('name' => _("nombre"), 'width' => 300),
            _("propio"),
            _("est. ok"),
            _("falta"),
            _("observ.")
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
        $mi_sfsv = core\ConfigGlobal::mi_sfsv();

        $this->oBotonesCurso = new web\BotonesCurso($this->modo_curso);
        $aWhere = $this->oBotonesCurso->getWhere();
        $aOperator = $this->oBotonesCurso->getOperator();

        $gesAsistente = new asistentes\GestorAsistente();
        $oPersona = personas\Persona::newPersona($this->id_pau);
        if (!is_object($oPersona)) {
            $this->msg_err = "<br>$oPersona con id_nom: $this->id_pau en  " . __FILE__ . ": line " . __LINE__;
            exit($this->msg_err);
        }
        // permisos Según el tipo de persona: n, agd, s
        $id_tabla = $oPersona->getId_tabla();
        $oPermDossier = new dossiers\PermDossier();
        $this->ref_perm = $oPermDossier->perm_activ_pers($id_tabla);

        $i = 0;
        $a_valores = array();
        $aWhereNom = ['id_nom' => $this->id_pau];
        $aOperadorNom = [];
        $cActividadesAsistente = $gesAsistente->getActividadesDeAsistente($aWhereNom, $aOperadorNom, $aWhere, $aOperator, TRUE);
        foreach ($cActividadesAsistente as $oAsistente) {
            $i++;
            $id_activ = $oAsistente->getId_activ();
            $id_tabla_asist = $oAsistente->getId_tabla();
            $oActividad = new actividades\Actividad($id_activ);
            $nom_activ = $oActividad->getNom_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $dl_org = $oActividad->getDl_org();
            $f_ini = $oActividad->getF_ini()->getFromLocal();
            $f_fin = $oActividad->getF_fin()->getFromLocal();

            $propio = $oAsistente->getPropio();
            $falta = $oAsistente->getFalta();
            $est_ok = $oAsistente->getEst_ok();
            $observ = $oAsistente->getObserv();

            $oTipoActividad = new web\TiposActividades($id_tipo_activ);
            $isfsv = $oTipoActividad->getSfsvId();
            // para ver el nombre en caso de la otra sección
            if ($mi_sfsv != $isfsv && !($_SESSION['oPerm']->have_perm_oficina('des'))) {
                $ssfsv = $oTipoActividad->getSfsvText();
                $sactividad = $oTipoActividad->getActividadText();
                $nom_activ = "$ssfsv $sactividad";
            }
            // para modificar.
            $id_tipo = substr($id_tipo_activ, 0, 3); //cojo los 3 primeros dígitos
            $act = !empty($this->ref_perm[$id_tipo]) ? $this->ref_perm[$id_tipo] : '';

            if (!empty($act["perm"])) {
                $permiso = 3;
            } else {
                $permiso = 1;
            }

            $propio == 't' ? $chk_propio = "si" : $chk_propio = "no";
            $falta == 't' ? $chk_falta = "si" : $chk_falta = "no";
            $est_ok == 't' ? $chk_est_ok = "si" : $chk_est_ok = "no";

            if ($permiso == 3) {
                $a_valores[$i]['sel'] = "$id_activ";
            } else {
                $a_valores[$i]['sel'] = "";
            }
            $a_valores[$i][1] = "$f_ini-$f_fin";
            $a_valores[$i][2] = $nom_activ;
            $a_valores[$i][3] = $chk_propio;
            $a_valores[$i][4] = $chk_est_ok;
            $a_valores[$i][5] = $chk_falta;
            $a_valores[$i][6] = $observ;
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
        $this->txt_eliminar = _("¿Está seguro que desea borrar a esta persona de esta actividad?");
        // En el caso de actualizar la misma página (fnjs_actualizar) solo me quedo con la última (stack=0).
        $oPosicion = new web\Posicion();
        $stack = $oPosicion->getStack(0);

        $oHashSelect = new web\Hash();
        $oHashSelect->setCamposForm('modo_curso');
        $oHashSelect->setCamposNo('sel!mod!scroll_id!refresh');
        $a_camposHidden = array(
            'pau' => $this->pau,
            'id_pau' => $this->id_pau,
            'obj_pau' => $this->obj_pau,
            'queSel' => $this->queSel,
            'id_dossier' => $this->id_dossier,
            'permiso' => 3,
            'stack' => $stack,
        );
        $oHashSelect->setArraycamposHidden($a_camposHidden);

        //Hay que ponerlo antes, para que calcule los chk.
        $oTabla = new web\Lista();
        $oTabla->setId_tabla('select1301');
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

        $oView = new core\View(__NAMESPACE__);
        return $oView->renderizar('select1301.phtml', $a_campos);
    }

    private function setLinksInsert()
    {
        $this->aLinks_dl = array();
        $this->aLinks_otros = array();
        $ref_perm = $this->ref_perm;
        if (empty($ref_perm)) { // si es nulo, no tengo permisos de ningún tipo
            return '';
        }
        $mi_dele = core\ConfigGlobal::mi_delef();
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
                $pagina = web\Hash::link('apps/asistentes/controller/form_1301.php?' . http_build_query($aQuery));
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
                $pagina = web\Hash::link('apps/asistentes/controller/form_1301.php?' . http_build_query($aQuery));
                $this->aLinks_otros[$nom] = $pagina;
            }
        }
    }


    public function setModo_curso($modo_curso)
    {
        if (empty($modo_curso)) {
            $modo_curso = 1;
        }
        $this->modo_curso = $modo_curso;
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

    public function getStatus()
    {
        return $this->status;
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

    public function setStatus($Qstatus)
    {
        $this->status = $Qstatus;
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
